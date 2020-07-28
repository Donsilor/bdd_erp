<?php

namespace addons\Purchase\backend\controllers;

use Yii;
use addons\Purchase\common\models\PurchaseParts;
use common\enums\AuditStatusEnum;
use addons\Purchase\common\enums\PurchaseStatusEnum;
use addons\Purchase\common\enums\PurchaseTypeEnum;
use common\helpers\ArrayHelper;
use common\helpers\SnHelper;
use common\models\base\SearchModel;
use common\traits\Curd;
/**
 *
 *
 * Class PurchasePartsController
 * @package backend\modules\goods\controllers
 */
class PurchasePartsController extends BaseController
{  
    use Curd;
    /**
     * @var PurchaseParts
     */
    public $modelClass = PurchaseParts::class;
    
    /**
     * 首页
     *
     * @return string
     * @throws
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
        
        return $this->render($this->action->id, [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
        ]);
    }
    /**
     * 详情展示页
     * @return string
     * @throws
     */
    public function actionView()
    {
        $id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab',1);
        
        $model = $this->findModel($id);
        return $this->render($this->action->id, [
                'model' => $model,
                'tab'=>$tab,
                'tabList'=>Yii::$app->purchaseService->parts->menuTabList($id,$this->returnUrl),
                'returnUrl'=>$this->returnUrl,
        ]);
    }
    /**
     * ajax编辑/创建
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxEdit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);

        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            if($model->isNewRecord){
                $model->purchase_sn = SnHelper::createPurchaseSn();
                $model->creator_id  = \Yii::$app->user->identity->id;
            }
            return $model->save()
            ? $this->redirect(Yii::$app->request->referrer)
            : $this->message($this->getError($model), $this->redirect(['index']), 'error');
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
        $this->returnUrl = \Yii::$app->request->referrer;
        
        if($model->purchase_status != PurchaseStatusEnum::SAVE){
            return $this->message('单据不是保存状态', $this->redirect($this->returnUrl), 'error');
        }
        $model->purchase_status = PurchaseStatusEnum::PENDING;
        $model->audit_status = AuditStatusEnum::PENDING;
        if(false === $model->save()){
            return $this->message($this->getError($model), $this->redirect($this->returnUrl), 'error');
        }
        return $this->message('操作成功', $this->redirect($this->returnUrl), 'success');
        
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
                //if($model->audit_status == AuditStatusEnum::PASS){
                    //Yii::$app->purchaseService->purchase->syncPurchaseToProduce($id);
                //}
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

            Yii::$app->purchaseService->purchase->syncPurchaseToReceipt($model, PurchaseTypeEnum::MATERIAL_GOLD);

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
    public function actionExport($ids=null){
        $name = '采购订单明细';
        if(!is_array($ids)){
            $ids = StringHelper::explodeIds($ids);
        }
        if(!$ids){
            return $this->message('采购订单ID不为空', $this->redirect(['index']), 'warning');
        }
        $list = [];
        //print_r($list);exit();

        $header = [

        ];

        return ExcelHelper::exportData($list, $header, $name.'数据导出_' . date('YmdHis',time()));
    }



}
