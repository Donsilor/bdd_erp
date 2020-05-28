<?php

namespace addons\Warehouse\backend\controllers;


use addons\Warehouse\common\enums\RepairStatusEnum;
use common\helpers\Url;
use Yii;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseBillRepair;
use addons\Warehouse\common\forms\WarehouseBillRepairForm;
use addons\Warehouse\common\enums\BillTypeEnum;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;
use common\helpers\ResultHelper;
use common\helpers\SnHelper;
use yii\base\Exception;
use common\traits\Curd;
/**
* RepairBill
*
* Class WarehouseBillRepairController
* @package backend\modules\goods\controllers
*/
class WarehouseBillRepairController extends BaseController
{
    use Curd;

    /**
    * @var WarehouseBillRepair
    */
    public $modelClass = WarehouseBillRepairForm::class;
    public $billType = BillTypeEnum::BILL_TYPE_WX;

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
            'pageSize' => $this->pageSize,
            'relations' => [
                'creator' => ['username'],
                'auditor' => ['username'],
                'follower' => ['username'],
            ]
        ]);
        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams);

        $dataProvider->query->andWhere(['>',WarehouseBillRepairForm::tableName().'.status',-1]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * 编辑/创建
     *
     * @return mixed
     */
    public function actionEditLang()
    {
        $id = Yii::$app->request->get('id');
        $returnUrl = Yii::$app->request->get('returnUrl',['index']);
        $model = $this->findModel($id);
        $model = $model ??new WarehouseBillRepair();

        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            if(!$model->validate()) {
                return ResultHelper::json(422, $this->getError($model));
            }
            try{
                $trans = Yii::$app->db->beginTransaction();
                if($model->isNewRecord) {
                    $model->repair_no = SnHelper::createBillSn($this->billType);
                }

                \Yii::$app->warehouseService->repair->createRepairBill($model);

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
        $model = $this->findModel($id);
        if($model->repair_act){
            $repair_act_arr = explode(',', $model->repair_act);
            $repair_act_str = '';
            foreach ($repair_act_arr as $repair_act){
                $repair_act_str .= ','. Yii::$app->attr->valueName($repair_act);
            }
            $model->repair_act = trim($repair_act_str,',' );
        }
        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * ajax 维修单-申请
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxApply(){
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        try{
            $trans = Yii::$app->trans->beginTransaction();

            \Yii::$app->warehouseService->repair->applyRepair($model);

            $trans->commit();
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message("申请失败:". $e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
        }
        return $this->message("申请成功", $this->redirect(Yii::$app->request->referrer), 'success');

    }


    /**
     * 维修单-审核
     *
     * @return mixed
     */
    public function actionAjaxAudit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);

        if($model->audit_status == AuditStatusEnum::PENDING) {
            $model->audit_status = AuditStatusEnum::PASS;
        }

        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();

                $model->audit_time = time();
                $model->auditor_id = \Yii::$app->user->identity->id;

                \Yii::$app->warehouseService->repair->auditRepair($model);

                $trans->commit();
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message("审核失败:". $e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
            }
            return $this->message("保存成功", $this->redirect(Yii::$app->request->referrer), 'success');
        }

        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * ajax 维修单-下单
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxOrders(){
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        try{
            $trans = Yii::$app->trans->beginTransaction();

            \Yii::$app->warehouseService->repair->ordersRepair($model);

            $trans->commit();
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message("下单失败:". $e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
        }
        return $this->message("下单成功", $this->redirect(Yii::$app->request->referrer), 'success');

    }

    /**
     * ajax 维修单-完毕
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxFinish(){
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        try{
            $trans = Yii::$app->trans->beginTransaction();

            \Yii::$app->warehouseService->repair->finishRepair($model);

            $trans->commit();
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message("操作失败:". $e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
        }
        return $this->message("操作成功", $this->redirect(Yii::$app->request->referrer), 'success');

    }

    /**
     * ajax 维修单-收货
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxReceiving(){
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        try{
            $trans = Yii::$app->trans->beginTransaction();

            \Yii::$app->warehouseService->repair->receivingRepair($model);

            $trans->commit();
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message("收货失败:". $e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
        }
        return $this->message("收货成功", $this->redirect(Yii::$app->request->referrer), 'success');

    }

}
