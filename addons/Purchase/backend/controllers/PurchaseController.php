<?php

namespace addons\Purchase\backend\controllers;

use Yii;
use common\enums\AuditStatusEnum;
use common\enums\LogTypeEnum;
use common\models\base\SearchModel;
use common\traits\Curd;
use common\helpers\SnHelper;
use addons\Purchase\common\forms\PurchaseFollowerForm;
use addons\Purchase\common\models\Purchase;
use addons\Purchase\common\enums\PurchaseTypeEnum;
use addons\Purchase\common\enums\PurchaseStatusEnum;

/**
 * 
 *
 * Class PurchaseController
 * @package backend\modules\goods\controllers
 */
class PurchaseController extends BaseController
{
    use Curd;
    
    /**
     * @var Purchase
     */     
    public $modelClass = Purchase::class;
    /**
     * @var int
     */
    public $purchaseType = PurchaseTypeEnum::GOODS;
    
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
            'tabList'=>Yii::$app->purchaseService->purchase->menuTabList($id,$this->purchaseType,$this->returnUrl),
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
                if($model->audit_status == AuditStatusEnum::PASS){
                    Yii::$app->purchaseService->purchase->syncPurchaseToProduce($id);
                }
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
     * 分配跟单人
     * @return mixed|string|\yii\web\Response|string
     */
    public function actionAjaxFollower(){
        
        $id = Yii::$app->request->get('id');
        
        $this->modelClass = PurchaseFollowerForm::class;
        $model = $this->findModel($id);
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();
                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }
                
                //日志
                $log = [
                        'purchase_id' => $id,
                        'purchase_sn' => $model->purchase_sn,
                        'log_type' => LogTypeEnum::ARTIFICIAL,
                        'log_module' => "分配跟单人",
                        'log_msg' => "分配跟单人：".$model->follower->usrname??''
                ];
                Yii::$app->purchaseService->purchase->createPurchaseLog($log);                 
                $trans->commit();  
                
                Yii::$app->getSession()->setFlash('success','保存成功');
                return $this->redirect(Yii::$app->request->referrer);                
            }catch (\Exception $e) {
                $trans->rollback();
                return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }


}
