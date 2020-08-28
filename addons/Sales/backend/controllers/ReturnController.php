<?php

namespace addons\Sales\backend\controllers;

use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\BillTypeEnum;
use addons\Warehouse\common\models\WarehouseBill;
use Yii;
use common\helpers\Url;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Sales\common\forms\ReturnForm;
use addons\Sales\common\enums\CheckStatusEnum;
use addons\Sales\common\enums\ReturnStatusEnum;
use common\enums\AuditStatusEnum;

/**
 * 退款单
 *
 * Class ReturnController
 * @package addons\Sales\backend\controllers
 */
class ReturnController extends BaseController
{
    use Curd;

    /**
     * @var ReturnForm
     */
    public $modelClass = ReturnForm::class;
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
            'partialMatchAttributes' => ['customer_name'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'creator' => ['username'],
                'auditor' => ['username'],
                'leader' => ['username'],
                'storekeeper' => ['username'],
                'finance' => ['username'],
                'payer' => ['username'],
            ]
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams,['created_at']);

        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=',ReturnForm::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',ReturnForm::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }

        //$dataProvider->query->andWhere(['>',ReturnForm::tableName().'.status',-1]);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * 主管审核
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionLeader()
    {
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['customer_name'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'creator' => ['username'],
                'auditor' => ['username'],
                'leader' => ['username'],
                'storekeeper' => ['username'],
                'finance' => ['username'],
                'payer' => ['username'],
            ]
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams,['created_at']);

        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=',ReturnForm::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',ReturnForm::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }

        //$dataProvider->query->andWhere(['=',ReturnForm::tableName().'.check_status',CheckStatusEnum::SAVE]);
        $dataProvider->query->andWhere(['>=',ReturnForm::tableName().'.audit_status',AuditStatusEnum::PENDING]);
        $dataProvider->query->andWhere(['>',ReturnForm::tableName().'.leader_status',AuditStatusEnum::SAVE]);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * 商品部审核
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionStorekeeper()
    {
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['customer_name'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'creator' => ['username'],
                'auditor' => ['username'],
                'leader' => ['username'],
                'storekeeper' => ['username'],
                'finance' => ['username'],
                'payer' => ['username'],
            ]
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams,['created_at']);

        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=',ReturnForm::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',ReturnForm::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }

        //$dataProvider->query->andWhere(['=',ReturnForm::tableName().'.check_status',CheckStatusEnum::LEADER]);
        $dataProvider->query->andWhere(['>=',ReturnForm::tableName().'.audit_status',AuditStatusEnum::PENDING]);
        $dataProvider->query->andWhere(['=',ReturnForm::tableName().'.leader_status',AuditStatusEnum::PASS]);
        $dataProvider->query->andWhere(['>',ReturnForm::tableName().'.storekeeper_status',AuditStatusEnum::SAVE]);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * 财务审核
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionFinance()
    {
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['customer_name'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'creator' => ['username'],
                'auditor' => ['username'],
                'leader' => ['username'],
                'storekeeper' => ['username'],
                'finance' => ['username'],
                'payer' => ['username'],
            ]
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams,['created_at']);

        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=',ReturnForm::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',ReturnForm::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }

        //$dataProvider->query->andWhere(['=',ReturnForm::tableName().'.check_status',CheckStatusEnum::STOREKEEPER]);
        $dataProvider->query->andWhere(['>=',ReturnForm::tableName().'.audit_status',AuditStatusEnum::PENDING]);
        $dataProvider->query->andWhere(['=',ReturnForm::tableName().'.leader_status',AuditStatusEnum::PASS]);
        $dataProvider->query->andWhere(['=',ReturnForm::tableName().'.storekeeper_status',AuditStatusEnum::PASS]);
        $dataProvider->query->andWhere(['>',ReturnForm::tableName().'.finance_status',AuditStatusEnum::SAVE]);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Ajax 编辑/创建
     * @throws
     * @return mixed
     */
    public function actionAjaxEdit()
    {
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new ReturnForm();
        $this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            try{
                $trans = \Yii::$app->db->beginTransaction();
                if($model->isNewRecord){
                    //$model->status = StatusEnum::DISABLED;
                }
                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }
                $trans->commit();
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
            }
            \Yii::$app->getSession()->setFlash('success','保存成功');
            return $this->redirect(\Yii::$app->request->referrer);
        }
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
 * 提交审核
 * @throws
 * @return mixed
 */
    public function actionAjaxApply()
    {
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new ReturnForm();
        if($model->audit_status != AuditStatusEnum::SAVE){
            return $this->message('退款单不是保存状态', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        try{
            $trans = Yii::$app->trans->beginTransaction();

            $model->audit_status = AuditStatusEnum::PENDING;
            $model->leader_status = AuditStatusEnum::PENDING;
            $model->return_status = ReturnStatusEnum::PENDING;
            if(false === $model->save()){
                throw new \Exception($this->getError($model));
            }
            $trans->commit();
            return $this->message('操作成功', $this->redirect(\Yii::$app->request->referrer), 'success');
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message("操作失败:". $e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
        }
    }

    /**
     * 退款-审核
     * @throws
     * @return mixed
     */
    public function actionAjaxAudit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new ReturnForm();
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try{
                $trans = Yii::$app->trans->beginTransaction();

                \Yii::$app->salesService->return->auditReturn($model);
                $trans->commit();
            }catch (\Exception $e){
                $trans->rollBack();
                return $this->message("保存失败:". $e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
            }
            return $this->message("保存成功", $this->redirect(Yii::$app->request->referrer), 'success');
        }

        if ($model->check_status == CheckStatusEnum::SAVE) {
            $status = "leader_status";
            $remark = "leader_remark";
        } elseif ($model->check_status == CheckStatusEnum::LEADER) {
            $status = "storekeeper_status";
            $remark = "storekeeper_remark";
        } elseif ($model->check_status == CheckStatusEnum::STOREKEEPER) {
            $status = "finance_status";
            $remark = "finance_remark";
        } else {
            $status = "audit_status";
            $remark = "audit_remark";
        }
        $model->$status = AuditStatusEnum::PASS;
        $bill = WarehouseBill::find()->where(['order_sn'=>$model->order_sn, 'bill_type'=> BillTypeEnum::BILL_TYPE_S, 'bill_status' => BillStatusEnum::CONFIRM])->one();
        if(!empty($bill)){
            $model->to_warehouse_id = $bill->from_warehouse_id;
        }
        return $this->renderAjax($this->action->id, [
            'model' => $model,
            'status' => $status,
            'remark' => $remark,
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
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['index', 'id'=>$id]));
        $model = $this->findModel($id);
        $model = $model ?? new ReturnForm();
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->salesService->return->menuTabList($id, $returnUrl),
            'returnUrl'=>$returnUrl,
        ]);
    }

    /**
     * 取消退款
     * @throws
     * @return mixed
     */
    public function actionCancel()
    {
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new ReturnForm();
        if($model->audit_status != AuditStatusEnum::SAVE){
            return $this->message('退款单不是保存状态', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        try{
            $trans = Yii::$app->trans->beginTransaction();

            \Yii::$app->salesService->return->cancelReturn($model);
            if(false === $model->save()){
                throw new \Exception($this->getError($model));
            }
            $trans->commit();
            return $this->message('操作成功', $this->redirect(\Yii::$app->request->referrer), 'success');
        }catch (\Exception $e){
            $trans->rollBack();
            return $this->message("操作失败:". $e->getMessage(),  $this->redirect(Yii::$app->request->referrer), 'error');
        }
    }
}