<?php

namespace addons\Supply\backend\controllers;

use addons\Purchase\common\forms\PurchaseGoodsForm;
use addons\Supply\common\models\SupplierFollower;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\models\WarehouseStoneBill;
use common\helpers\ResultHelper;
use common\helpers\StringHelper;
use common\helpers\Url;
use Yii;
use common\models\base\SearchModel;
use addons\Supply\common\models\Supplier;
use addons\Supply\common\forms\SupplierAuditForm;
use addons\Supply\common\forms\SupplierForm;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;
use yii\base\Exception;
use common\traits\Curd;
/**
* Supplier
*
* Class SupplierController
* @package backend\modules\goods\controllers
*/
class SupplierController extends BaseController
{
    use Curd;

    /**
    * @var Supplier
    */
    public $modelClass = SupplierForm::class;


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
            'pageSize' => $this->pageSize
        ]);
        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);

        $dataProvider->query->andWhere(['>',Supplier::tableName().'.status',-1]);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * 编辑/创建
     *
     * @return mixed
     */
    public function actionEdit()
    {
        $id = Yii::$app->request->get('id');
        $returnUrl = Yii::$app->request->get('returnUrl',['index']);

        $model = $this->findModel($id);
        $model = $model ?? new Supplier();

        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            if(!$model->validate()) {
                return ResultHelper::json(422, $this->getError($model));
            }
            try{
                $trans = Yii::$app->db->beginTransaction();
                $model->status = StatusEnum::DISABLED;
                $model->supplier_code = StringHelper::getFirstCode($model->supplier_name);
                if(false === $model->save()){
                    throw new Exception($this->getError($model));
                }
                $trans->commit();
            }catch (Exception $e){
                $trans->rollBack();
                $error = $e->getMessage();
                \Yii::error($error);
                return $this->message("保存失败:".$error, $this->redirect([$this->action->id,'id'=>$model->id]), 'error');
            }

            return $this->message("保存成功", $this->redirect($returnUrl), 'success');
        }

        return $this->render($this->action->id, [
            'model' => $model,
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
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['supplier/index']));
        $model = $this->findModel($id);
        $model = $model ?? new Supplier();
        if($model->business_scope){
            $business_scope_arr = explode(',', $model->business_scope);
            $business_scope_str = '';
            foreach ($business_scope_arr as $business_scope){
                $business_scope_str .= ','. \addons\Supply\common\enums\BusinessScopeEnum::getValue($business_scope);
            }
            $model->business_scope = trim( $business_scope_str,',' );
        }

        if($model->pay_type){
            $pay_type_arr = explode(',', $model->pay_type);
            $pay_type_str = '';
            foreach ($pay_type_arr as $pay_type){
                $pay_type_str .= ','. \addons\Supply\common\enums\SettlementWayEnum::getValue($pay_type);
            }
            $model->pay_type = trim( $pay_type_str,',' );
        }
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->supplyService->supplier->menuTabList($id,$returnUrl),
            'returnUrl'=>$returnUrl,
        ]);
    }

    /**
     * @return mixed
     * 提交审核
     */
    public function actionAjaxApply(){
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new Supplier();
        if($model->audit_status != AuditStatusEnum::SAVE){
            return $this->message('供应商不是保存状态', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        $model->audit_status = AuditStatusEnum::PENDING;
        if(false === $model->save()){
            return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        return $this->message('操作成功', $this->redirect(\Yii::$app->request->referrer), 'success');
    }

    /**
     * 供应商-审核
     *
     * @return mixed
     */
    public function actionAjaxAudit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new Supplier();
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();
                if($model->audit_status == AuditStatusEnum::PASS){
                    $model->auditor_id = \Yii::$app->user->id;
                    $model->audit_time = time();
                    $model->status = StatusEnum::ENABLED;
                }else{
                    $model->status = StatusEnum::DISABLED;
                    $model->audit_status = AuditStatusEnum::SAVE;
                }
                if(false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
                $trans->commit();
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message("审核失败:". $e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
            }
            return $this->message("保存成功", $this->redirect(Yii::$app->request->referrer), 'success');
        }
        $model->audit_status  = AuditStatusEnum::PASS;
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }
}
