<?php

namespace addons\Purchase\backend\controllers;

use addons\Purchase\common\enums\PurchaseTypeEnum;
use common\helpers\ArrayHelper;
use Yii;
use addons\Purchase\common\models\PurchaseGold;
use common\enums\AuditStatusEnum;
use addons\Purchase\common\enums\PurchaseStatusEnum;
use common\helpers\SnHelper;
use common\models\base\SearchModel;
use common\traits\Curd;
use common\enums\LogTypeEnum;
/**
 *
 * 物料基础控制器
 * Class PurchaseMaterialController
 * @package backend\modules\goods\controllers
 */
class PurchaseMaterialController extends BaseController
{  
    use Curd;
    /**
     * @var PurchaseGold
     */
    public $modelClass;
    /**
     * @var PurchaseTypeEnum
     */
    public $purchaseType;
        
    /**
     * 首页
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex()
    {
        $searchModel = new SearchModel([
                'model' => $this->modelClass,
                'scenario' => 'default',
                'partialMatchAttributes' => [], // 模糊查询
                'defaultOrder' => [
                        'id' => SORT_DESC
                ],
                'pageSize' => $this->getPageSize(),
                'relations' => [
                        
                ]
        ]);
        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);        
        $dataProvider->query->andWhere(['>','status',-1]);
        //导出
        if(\Yii::$app->request->get('action') === 'export'){
            $dataProvider->setPagination(false);
            $list = $dataProvider->models;
            $list = ArrayHelper::toArray($list);
            $ids = array_column($list,'id');
            $this->actionExport($ids);
        }
        
        return $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
        ]);
    }
    /**
     * 详情展示页
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView()
    {
        $id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab',1);
        
        $model = $this->findModel($id);
        return $this->render($this->action->id, [
                'model' => $model,
                'tab'=>$tab,
                'tabList'=>Yii::$app->purchaseService->gold->menuTabList($id,$this->returnUrl),
                'returnUrl'=>$this->returnUrl,
        ]);
    }
    /**
     * ajax编辑/创建
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     *  
    */
    public function actionAjaxEdit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $isNewRecord = $model->isNewRecord;
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->db->beginTransaction();
                if($isNewRecord){
                    $model->purchase_sn = SnHelper::createPurchaseSn();
                    $model->creator_id  = \Yii::$app->user->identity->id;
                }
                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }
                if($isNewRecord) {
                    //日志
                    $log = [
                            'purchase_id' => $model->id,
                            'purchase_sn' => $model->purchase_sn,
                            'log_type' => LogTypeEnum::ARTIFICIAL,
                            'log_module' => "创建单据",
                            'log_msg' => "创建采购单，单号:".$model->purchase_sn
                    ];
                    Yii::$app->purchaseService->purchaseLog->createPurchaseLog($log,$this->purchaseType);
                    $trans->commit();
                    return $this->message("保存成功", $this->redirect(['view', 'id' => $model->id]), 'success');
                }else{
                    $trans->commit();
                    return $this->redirect(Yii::$app->request->referrer);
                }
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }
        return $this->renderAjax($this->action->id, [
                'model' => $model,
        ]);
    }
    
    /**
     * 申请审核
     * @return mixed
     */
    public function actionAjaxApply(){
        
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        
        if($model->purchase_status != PurchaseStatusEnum::SAVE){
            return $this->message('单据不是保存状态', $this->redirect($this->returnUrl), 'error');
        }
        try{
            $trans = Yii::$app->db->beginTransaction();           
            
            $model->purchase_status = PurchaseStatusEnum::PENDING;
            $model->audit_status = AuditStatusEnum::PENDING;
            if(false === $model->save()){
                throw new \Exception($this->getError($model));
            }
            //日志
            $log = [
                    'purchase_id' => $model->id,
                    'purchase_sn' => $model->purchase_sn,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_module' => "申请审核",
                    'log_msg' => "采购单申请审核",
            ];
            Yii::$app->purchaseService->purchaseLog->createPurchaseLog($log,$this->purchaseType);
            $trans->commit();
            return $this->message('操作成功', $this->redirect(Yii::$app->request->referrer), 'success');
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
        }
    }
    
    /**
     * ajax 批量审核
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxAudit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        if(!$model->audit_status) {
            $model->audit_status = AuditStatusEnum::PASS;
        }
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->db->beginTransaction();
                $model->audit_time = time();
                $model->auditor_id = \Yii::$app->user->identity->id;
                if($model->audit_status == AuditStatusEnum::PASS){
                    $model->purchase_status = PurchaseStatusEnum::CONFIRM;
                }else{
                    $model->purchase_status = PurchaseStatusEnum::SAVE;
                }
                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }                
                //日志
                $log = [
                        'purchase_id' => $model->id,
                        'purchase_sn' => $model->purchase_sn,
                        'log_type' => LogTypeEnum::ARTIFICIAL,
                        'log_module' => "单据审核",
                        'log_msg' => "采购单审核, 审核状态：".AuditStatusEnum::getValue($model->audit_status).",审核备注：".$model->audit_remark
                ];
                Yii::$app->purchaseService->purchaseLog->createPurchaseLog($log,$this->purchaseType);
                
                $trans->commit();
                Yii::$app->getSession()->setFlash('success','保存成功');
                return $this->redirect(Yii::$app->request->referrer);
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }
            
        }
        
        return $this->renderAjax($this->action->id, [
                'model' => $model,
        ]);
    }

    /**
     * ajax 申请收货
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxReceipt()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        try{
            $trans = Yii::$app->db->beginTransaction();

            Yii::$app->purchaseService->purchase->syncPurchaseToReceipt($model, $this->purchaseType);

            $trans->commit();
            return $this->message('操作成功', $this->redirect(\Yii::$app->request->referrer), 'success');
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message('操作失败，'.$e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }

    }

    /**
     * @param null $ids
     * @return bool|mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function actionExport($ids = null){
        
    }



}
