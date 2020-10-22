<?php

namespace addons\Warehouse\backend\controllers;

use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\BillTypeEnum;
use common\enums\AuditStatusEnum;
use addons\Warehouse\common\forms\WarehouseBillCForm;
use addons\Warehouse\common\models\Warehouse;
use common\enums\LogTypeEnum;
use addons\Warehouse\common\forms\WarehouseBillThForm;
use addons\Warehouse\common\models\WarehouseBillGoods;

/**
 * 其它退货单
 * 
 */
class BillThController extends BaseController
{
    use Curd;
    
    public $modelClass = WarehouseBillThForm::class;
    public $billType = BillTypeEnum::BILL_TYPE_TH;
    public $billFix  = BillTypeEnum::BILL_TYPE_TH;

    /**
     * Lists all WarehouseBill models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['remark'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'creator' => ['username'],
                'auditor' => ['username'],
                'salesman' => ['username'],
            ]
        ]);

        $dataProvider = $searchModel
            ->search(\Yii::$app->request->queryParams, ['created_at', 'audit_time']);

        if (!empty($searchModel->created_at)) {
            $created_ats = explode('/', $searchModel->created_at);
            $dataProvider->query->andFilterWhere(['>=', Warehousebill::tableName() . '.created_at', strtotime($created_ats[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<', Warehousebill::tableName() . '.created_at', strtotime($created_ats[1]) + 86400]);//结束时间
        }
        
        if (!empty($searchModel->audit_time)) {
            $audit_times = explode('/', $searchModel->audit_time);
            $dataProvider->query->andFilterWhere(['>=', Warehousebill::tableName() . '.audit_time', strtotime($audit_times[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<', Warehousebill::tableName() . '.audit_time', strtotime($audit_times[1]) + 86400]);//结束时间
        }

        $dataProvider->query->andWhere(['>', Warehousebill::tableName() . '.status', -1]);
        $dataProvider->query->andWhere(['=', Warehousebill::tableName() . '.bill_type', $this->billType]);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * ajax编辑/创建 其它退货单
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxEdit()
    {
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            $isNewRecord = $model->isNewRecord;
            if ($model->isNewRecord) {
                $model->bill_type = $this->billType;
                $model->bill_no = \Yii::$app->warehouseService->bill->createBillSn($this->billFix);
            }
            try {
                $trans = \Yii::$app->db->beginTransaction();
                if (false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
                if ($isNewRecord) {             
                    $log = [
                        'bill_id' => $model->id,
                        'log_type' => LogTypeEnum::ARTIFICIAL,
                        'log_module' => '其它退货单',
                        'log_msg' => "创建其它退货单, 单据编号：{$model->bill_no} "
                    ];
                    \Yii::$app->warehouseService->billLog->createBillLog($log);
                }
                $trans->commit();
                if ($isNewRecord) {
                    return $this->message("保存成功", $this->redirect(['bill-th-goods/index', 'bill_id' => $model->id]), 'success');
                } else {
                    return $this->message('保存成功', $this->redirect(Yii::$app->request->referrer), 'success');
                }
            } catch (\Exception $e) {
                $trans->rollBack();
                return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
            }
        }
        return $this->renderAjax($this->action->id, [
            'model' => $model,
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
        $model = $this->findModel($id);        
        return $this->render($this->action->id, [
            'model' => $model,
            'tab' => Yii::$app->request->get('tab', 1),
            'tabList' => \Yii::$app->warehouseService->bill->menuTabList($id, $this->billType, $this->returnUrl),
            'returnUrl' => $this->returnUrl,
        ]);
    }

    /**
     * ajax 其它退货单-申请审核
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxApply($id)
    {
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        if ($model->bill_status != BillStatusEnum::SAVE) {
            return $this->message('单据不是保存状态', $this->redirect(\Yii::$app->request->referrer), 'error');
        }        
        $count = WarehouseBillGoods::find()->where(['bill_id'=>$id, 'goods_num'=>0])->count();
        if($count > 0){
            return $this->message("有{$count}个货号未填写退货数量", $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        if ($model->goods_num <= 0) {
            return $this->message('单据明细不能为空', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        try {

            $trans = \Yii::$app->trans->beginTransaction();
            $model->bill_status = BillStatusEnum::PENDING;
            $model->audit_status = AuditStatusEnum::PENDING;
            if (false === $model->save()) {
                return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
            }
            //日志
            $log = [
                'bill_id' => $model->id,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => '提交审核',
                'log_msg' => "其它退货单申请审核"
            ];
            \Yii::$app->warehouseService->billLog->createBillLog($log);
            $trans->commit();
            return $this->message('操作成功', $this->redirect(\Yii::$app->request->referrer), 'success');

        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
    }

    /**
     * ajax 其它退货单-审核
     *
     * @return mixed|string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionAjaxAudit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try {
                $trans = \Yii::$app->trans->beginTransaction();
                
                \Yii::$app->warehouseService->billTh->audit($model);
                $trans->commit();
                
                $this->message('操作成功', $this->redirect(Yii::$app->request->referrer), 'success');
            } catch (\Exception $e) {
                $trans->rollBack();
                $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }
        
        if ($model->audit_status == AuditStatusEnum::PENDING) {
            $model->audit_status = AuditStatusEnum::PASS;
        }
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * 其它退货单-关闭
     *
     * @param $id
     * @return mixed
     */
    public function actionCancel($id)
    {
        $this->modelClass = WarehouseBill::class;
        if (!($model = $this->modelClass::findOne($id))) {
            return $this->message("找不到数据", $this->redirect(Yii::$app->request->referrer), 'error');
        }
        try {
            $trans = \Yii::$app->trans->beginTransaction();

            \Yii::$app->warehouseService->billTh->cancel($model);

            $trans->commit();
            $this->message('操作成功', $this->redirect(Yii::$app->request->referrer), 'success');
        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
    }

    /**
     * 其它退货单-删除
     *
     * @param $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->modelClass = WarehouseBill::class;
        if (!($model = $this->modelClass::findOne($id))) {
            return $this->message("找不到数据", $this->redirect(Yii::$app->request->referrer), 'error');
        }
        try {
            $trans = \Yii::$app->trans->beginTransaction();
            \Yii::$app->warehouseService->billTh->delete($model);
            $trans->commit();
            $this->message('操作成功', $this->redirect(Yii::$app->request->referrer), 'success');
        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
    }
    
}
