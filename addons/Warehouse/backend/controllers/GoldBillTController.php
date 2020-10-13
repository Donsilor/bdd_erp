<?php

namespace addons\Warehouse\backend\controllers;

use addons\Supply\common\models\Supplier;
use addons\Warehouse\common\enums\GoldBillTypeEnum;
use addons\Warehouse\common\forms\WarehouseGoldBillTForm;
use addons\Warehouse\common\models\WarehouseGoldBill;
use addons\Warehouse\common\models\WarehouseGoldBillGoods;
use common\models\backend\Member;
use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use common\helpers\ExcelHelper;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseBillGoodsL;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Style\common\enums\LogTypeEnum;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;
use common\enums\AuditStatusEnum;
use common\helpers\PageHelper;
use common\helpers\SnHelper;
use common\helpers\Url;

/**
 * WarehouseBillController implements the CRUD actions for WarehouseBillController model.
 */
class GoldBillTController extends BaseController
{

    use Curd;
    public $modelClass = WarehouseGoldBillTForm::class;
    public $billType = GoldBillTypeEnum::GOLD_T;


    /**
     * Lists all StyleChannel models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['name'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'creator' => ['username'],
                'auditor' => ['username'],

            ]
        ]);

        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams, ['created_at', 'audit_time']);
        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=', WarehouseGoldBill::tableName() . '.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<', WarehouseGoldBill::tableName() . '.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)]);//结束时间
        }
        $audit_time = $searchModel->audit_time;
        if (!empty($audit_time)) {
            $dataProvider->query->andFilterWhere(['>=', WarehouseGoldBill::tableName() . '.audit_time', strtotime(explode('/', $audit_time)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<', WarehouseGoldBill::tableName() . '.audit_time', (strtotime(explode('/', $audit_time)[1]) + 86400)]);//结束时间
        }
        $dataProvider->query->andWhere(['>', WarehouseGoldBill::tableName() . '.status', -1]);
        $dataProvider->query->andWhere(['=', WarehouseGoldBill::tableName() . '.bill_type', $this->billType]);

        //导出
        if (\Yii::$app->request->get('action') === 'export') {
            $dataProvider->setPagination(false);
            $list = $dataProvider->models;
            $list = ArrayHelper::toArray($list);
            $ids = array_column($list, 'id');
            $this->actionExport($ids);
        }

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
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
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseGoldBillTForm();
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            try {
                $trans = \Yii::$app->db->beginTransaction();
                $isNewRecord = $model->isNewRecord;
                if ($isNewRecord) {
                    $model->bill_no = SnHelper::createBillSn($this->billType);
                    $model->bill_type = $this->billType;
                    $model->to_warehouse_id = 7;

                }

                if (false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }

                if ($isNewRecord) {
//                    $gModel = new WarehouseBillTGoodsForm();
//                    $gModel->bill_id = $model->id;
//                    $gModel->supplier_id = $model->supplier_id;
//                    $gModel->put_in_type = $model->put_in_type;
//                    $gModel->supplier_id = $model->supplier_id;
//                    $gModel->file = UploadedFile::getInstance($model, 'file');
//                    if (!empty($gModel->file) && isset($gModel->file)) {
//                        \Yii::$app->warehouseService->billT->uploadGoods($gModel);
//                    }
                    $log_msg = "创建其它入库单{$model->bill_no}";
                } else {
                    $log_msg = "修改其它入库单{$model->bill_no}";
                }
                $log = [
                    'bill_id' => $model->id,
                    'bill_status' => $model->bill_status,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_module' => '其它入库单',
                    'log_msg' => $log_msg
                ];
                \Yii::$app->warehouseService->goldBillLog->createGoldBillLog($log);

                \Yii::$app->warehouseService->goldBill->goldBillSummary($model->id);
                $trans->commit();

                if ($isNewRecord) {
                    \Yii::$app->getSession()->setFlash('success', '保存成功');
                    return $this->redirect(['gold-bill-t-goods/index', 'bill_id' => $model->id]);
                    //return $this->message("保存成功", $this->redirect(['view', 'id' => $model->id]), 'success');
                } else {
                    \Yii::$app->getSession()->setFlash('success', '保存成功');
                    return $this->redirect(\Yii::$app->request->referrer);
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
        $tab = Yii::$app->request->get('tab', 1);
        $returnUrl = Yii::$app->request->get('returnUrl', Url::to(['bill-t/index', 'id' => $id]));
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseBill();
        return $this->render($this->action->id, [
            'model' => $model,
            'tab' => $tab,
            'tabList' => \Yii::$app->warehouseService->goldBill->menuTabList($id, $this->billType, $returnUrl),
            'returnUrl' => $returnUrl,
        ]);
    }

    /**
     * @return mixed
     * 提交审核
     */
    public function actionAjaxApply()
    {
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseGoldBill();
        if ($model->bill_status != BillStatusEnum::SAVE) {
            return $this->message('单据不是保存状态', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        if ($model->total_num <= 0) {
            return $this->message('单据明细不能为空', $this->redirect(\Yii::$app->request->referrer), 'error');
        }
        $trans = \Yii::$app->db->beginTransaction();
        try {
            $model->bill_status = BillStatusEnum::PENDING;
            $model->audit_status = AuditStatusEnum::PENDING;
            if (false === $model->save()) {
                return $this->message($this->getError($model), $this->redirect(\Yii::$app->request->referrer), 'error');
            }

            $log = [
                'bill_id' => $model->id,
                'bill_status' => $model->bill_status,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => '其它入库单',
                'log_msg' => '单据提审'
            ];
            \Yii::$app->warehouseService->goldBillLog->createGoldBillLog($log);

            $trans->commit();
            return $this->message('操作成功', $this->redirect(\Yii::$app->request->referrer), 'success');

        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }

    }

    /**
     *
     * ajax收货单审核
     * @return mixed|string|\yii\web\Response
     * @throws
     */
    public function actionAjaxAudit()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseGoldBill();
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(Yii::$app->request->post())) {
            try {
                $trans = Yii::$app->trans->beginTransaction();
                $model->audit_time = time();
                $model->auditor_id = Yii::$app->user->identity->getId();
                if($model->audit_status == AuditStatusEnum::PASS){
                    $model->bill_status = BillStatusEnum::CONFIRM;
                    \Yii::$app->warehouseService->goldT->createGold($model);
                }else{
                    $model->bill_status = BillStatusEnum::SAVE;
                }
                if(false === $model->save()){
                    throw new \Exception($this->getError($model));
                }
                $log = [
                    'bill_id' => $model->id,
                    'bill_status' => $model->bill_status,
                    'log_type' => LogTypeEnum::ARTIFICIAL,
                    'log_module' => '其它入库单',
                    'log_msg' => '单据审核'
                ];
                \Yii::$app->warehouseService->goldBillLog->createGoldBillLog($log);
                $trans->commit();
                return $this->message("保存成功", $this->redirect(Yii::$app->request->referrer), 'success');
            } catch (\Exception $e) {
                $trans->rollBack();
                return $this->message("审核失败:" . $e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
            }
        }
        $model->audit_status = AuditStatusEnum::PASS;
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     *
     * 同步更新价格
     * @param $id
     * @return mixed
     */
    /* public function actionSyncUpdatePrice($id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return $this->message("找不到数据", $this->redirect(['index']), 'error');
        }
        try {
            $trans = \Yii::$app->db->beginTransaction();

            \Yii::$app->warehouseService->billT->syncUpdatePriceAll($id);

            //更新收货单汇总：总金额和总数量
            $res = \Yii::$app->warehouseService->billT->warehouseBillTSummary($id);
            if (false === $res) {
                throw new \yii\db\Exception('更新单据汇总失败');
            }
            $trans->commit();
            \Yii::$app->getSession()->setFlash('success', '更新成功');
            return $this->redirect(\Yii::$app->request->referrer);
        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
    } */

    /**
     *
     * 取消单据
     * @param $id
     * @return mixed
     * @throws
     */
    public function actionCancel($id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return $this->message("找不到数据", $this->redirect(['index']), 'error');
        }
        try {
            $trans = \Yii::$app->db->beginTransaction();
            $model->bill_status = BillStatusEnum::CANCEL;
            if (false === $model->save()) {
                throw new \Exception($this->getError($model));
            }
            //日志
            $log = [
                'bill_id' => $model->id,
                'bill_status' => $model->bill_status,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => '单据取消',
                'log_msg' => '取消其它收货单'
            ];
            \Yii::$app->warehouseService->goldBillLog->createGoldBillLog($log);
            \Yii::$app->getSession()->setFlash('success', '操作成功');
            $trans->commit();
            return $this->redirect(\Yii::$app->request->referrer);
        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
    }

    /**
     *
     * 删除单据
     * @param $id
     * @return mixed
     * @throws
     */
    public function actionDelete($id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return $this->message("找不到数据", $this->redirect(['index']), 'error');
        }
        try {
            $trans = \Yii::$app->db->beginTransaction();

            if (false === WarehouseBillGoodsL::deleteAll(['bill_id' => $id])) {
                throw new \Exception("单据明细删除失败");
            }
            if (false === $model->delete()) {
                throw new \Exception($this->getError($model));
            }
            $log = [
                'bill_id' => $model->id,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_module' => '单据删除',
                'log_msg' => '删除其它入库单'
            ];
            \Yii::$app->warehouseService->billLog->createBillLog($log);
            \Yii::$app->getSession()->setFlash('success', '操作成功');
            $trans->commit();
            return $this->redirect(\Yii::$app->request->referrer);
        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
    }

    /**
     * 单据打印
     * @return string
     * @throws
     */
    public function actionPrint()
    {
        $this->layout = '@backend/views/layouts/print';
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);
        list($lists, $total) = $this->getData($id);
        return $this->render($this->action->id, [
            'model' => $model,
            'lists' => $lists,
            'total' => $total
        ]);
    }

    /**
     * 单据导出
     * @param null $ids
     * @return bool|mixed
     * @throws
     */
    public function actionExport($ids = null)
    {
        $name = '(金料)其他入库单明细';
        if (!is_array($ids)) {
            $ids = StringHelper::explodeIds($ids);
        }
        if (!$ids) {
            return $this->message('单据ID不为空', $this->redirect(['index']), 'warning');
        }
        list($list,) = $this->getData($ids);

        $header = [
            ['入库单号', 'bill_no', 'text'],
            ['供应商', 'supplier_name', 'text'],
            ['创建人', 'creator_name', 'text'],
            ['审核时间', 'audit_time', 'date','Y-m-d'],
            ['原料名称', 'gold_name', 'text'],
            ['编号', 'gold_sn', 'text'],
            ['材质', 'gold_type', 'text'],
            ['重量(g)', 'gold_weight', 'text'],
            ['单价', 'gold_price', 'text'],
            ['总金额', 'cost_price', 'text'],
            ['备注', 'remark', 'text'],

        ];
        return ExcelHelper::exportData($list, $header, $name . '数据导出_' . date('YmdHis', time()));
    }

    private function getData($id)
    {
        $select = ['wg.*','wb.audit_time','sup.supplier_name as supplier_name','m.username as creator_name'];

        $query = WarehouseGoldBill::find()->alias('wb')
                ->leftJoin(WarehouseGoldBillGoods::tableName(). ' wg', 'wg.bill_id=wb.id')
                ->leftJoin(Supplier::tableName(). ' sup', 'sup.id=wb.supplier_id')
                ->leftJoin(Member::tableName(). ' m','m.id=wb.creator_id ' )
           ->where(['wb.id' => $id])->select($select);
        $lists = PageHelper::findAll($query, 100);
//        echo '<pre>';
//        print_r($bill);die;
        $total = [
            'cost_price' => 0,
            'gold_weight' => 0,
        ];
        foreach ($lists as &$list) {
            //金料类型
            $gold_type = empty($list['gold_type']) ? 0 : $list['gold_type'];
            $list['gold_type'] = Yii::$app->attr->valueName($gold_type);

            //汇总
            $total['cost_price'] = bcadd($total['cost_price'], $list['cost_price'], 2);//总成本价
            $total['gold_weight'] = bcadd($total['gold_weight'], $list['gold_weight'], 3);//总成本价
        }
        return [$lists, $total];
    }

}
