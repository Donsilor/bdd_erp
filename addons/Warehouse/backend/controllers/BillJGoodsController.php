<?php

namespace addons\Warehouse\backend\controllers;

use Yii;
use common\traits\Curd;
use common\helpers\Url;
use common\models\base\SearchModel;
use addons\Warehouse\common\enums\BillTypeEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Warehouse\common\models\WarehouseBillGoodsJ;
use addons\Warehouse\common\forms\WarehouseBillJForm;
use addons\Warehouse\common\forms\WarehouseBillJGoodsForm;
use addons\Warehouse\common\enums\AdjustTypeEnum;
use addons\Warehouse\common\enums\LendStatusEnum;
use addons\Warehouse\common\enums\QcStatusEnum;
use common\helpers\ArrayHelper;
use common\helpers\ResultHelper;

/**
 * WarehouseBillBGoodsController implements the CRUD actions for WarehouseBillBGoodsController model.
 */
class BillJGoodsController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseBillJGoodsForm::class;
    public $billType = BillTypeEnum::BILL_TYPE_J;

    /**
     * 单据明细列表
     * @return mixed
     */
    public function actionIndex()
    {
        $tab = Yii::$app->request->get('tab', 2);
        $returnUrl = Yii::$app->request->get('returnUrl', Url::to(['bill-j/index']));
        $bill_id = Yii::$app->request->get('bill_id');
        $bill = WarehouseBillJForm::findOne($bill_id);
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'goodsJ' => ['lend_status', 'receive_id', 'receive_time', 'receive_remark', 'restore_time', 'qc_status', 'qc_remark',],
            ]
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['=', WarehouseBillJGoodsForm::tableName() . '.bill_id', $bill_id]);
        $dataProvider->query->andWhere(['>', WarehouseBillJGoodsForm::tableName() . '.status', -1]);
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'bill' => $bill,
            'tab' => $tab,
            'tabList' => \Yii::$app->warehouseService->bill->menuTabList($bill_id, $this->billType, $returnUrl),
        ]);
    }

    /**
     *
     * 添加明细
     * @return mixed
     * @throws
     */
    public function actionAdd()
    {
        $this->layout = '@backend/views/layouts/iframe';

        $bill_id = Yii::$app->request->get('bill_id');
        $this->modelClass = WarehouseBillJGoodsForm::class;
        $form = $this->findModel($bill_id);
        $form = $form ?? new WarehouseBillJGoodsForm();
        if (\Yii::$app->request->post("search") == 1 && $form->load(\Yii::$app->request->post())) {
            $form->validateGoodsList();//查询校验
            $searchModel = new SearchModel([
                'model' => WarehouseGoods::class,
                'scenario' => 'default',
                'partialMatchAttributes' => [], // 模糊查询
                'defaultOrder' => [],
                'pageSize' => 100,
                'relations' => []
            ]);
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $dataProvider->query->andWhere(['goods_id' => $form->getGoodsIds()]);
            return $this->render($this->action->id, [
                'model' => $form,
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
            ]);
        }
        if ($form->load(\Yii::$app->request->post())) {
            try {
                $trans = Yii::$app->db->beginTransaction();
                $form->bill_id = $bill_id;
                //批量添加商品
                \Yii::$app->warehouseService->billJ->batchAddGoods($form);
                $trans->commit();
                return $this->message('保存成功', $this->redirect(Yii::$app->request->referrer), 'success');
            } catch (\Exception $e) {
                $trans->rollBack();
                return ResultHelper::json(424, $e->getMessage());
            }
        }
        return $this->render($this->action->id, [
            'model' => $form,
        ]);
    }

    /**
     *
     * 扫码添加
     * @return mixed
     * @throws
     */
    public function actionAjaxScan()
    {
        $id = Yii::$app->request->post('bill_id');
        $goods_id = Yii::$app->request->post('goods_id');
        if (!$goods_id) {
            \Yii::$app->getSession()->setFlash('error', '条码货号不能为空');
            return ResultHelper::json(422, "条码货号不能为空");
        }
        try {
            $trans = \Yii::$app->db->beginTransaction();

            \Yii::$app->warehouseService->billJ->scanAddGoods($id, [$goods_id]);
            $trans->commit();
            \Yii::$app->getSession()->setFlash('success', '添加成功');
            return ResultHelper::json(200, "添加成功");
        } catch (\Exception $e) {
            $trans->rollBack();
            \Yii::$app->getSession()->setFlash('error', $e->getMessage());
            return ResultHelper::json(422, $e->getMessage());
        }
    }

    /**
     *
     * 批量接收
     * @return mixed
     * @throws \yii\base\ExitException
     */
    public function actionBatchReceive()
    {
        $ids = \Yii::$app->request->get('ids');
        $bill_id = \Yii::$app->request->get('bill_id');
        $check = \Yii::$app->request->get('check', null);
        $model = new WarehouseBillJGoodsForm();
        $model->ids = $ids;
        if ($check) {
            try {
                \Yii::$app->warehouseService->billJ->receiveValidate($model);
                return ResultHelper::json(200, '', ['url' => Url::to([$this->action->id, 'bill_id' => $bill_id, 'ids' => $ids])]);
            } catch (\Exception $e) {
                return ResultHelper::json(422, $e->getMessage());
            }
        }
        if ($model->load(\Yii::$app->request->post())) {
            try {
                $trans = \Yii::$app->trans->beginTransaction();
                \Yii::$app->warehouseService->billJ->receiveGoods($model);

                $trans->commit();
                \Yii::$app->getSession()->setFlash('success', '保存成功');
                return ResultHelper::json(200, '保存成功');
            } catch (\Exception $e) {
                $trans->rollBack();
                return ResultHelper::json(422, $e->getMessage());
            }
        }

        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * 批量还货
     *
     * @return mixed
     * @throws \yii\base\ExitException
     */
    public function actionBatchReturn()
    {
        $ids = \Yii::$app->request->get('ids');
        $bill_id = \Yii::$app->request->get('bill_id');
        $check = \Yii::$app->request->get('check', null);
        $model = new WarehouseBillJGoodsForm();
        $model->ids = $ids;
        if ($check) {
            try {
                \Yii::$app->warehouseService->billJ->returnValidate($model);
                return ResultHelper::json(200, '', ['url' => Url::to([$this->action->id, 'bill_id' => $bill_id, 'ids' => $ids])]);
            } catch (\Exception $e) {
                return ResultHelper::json(422, $e->getMessage());
            }
        }
        if ($model->load(\Yii::$app->request->post())) {
            try {
                $trans = \Yii::$app->trans->beginTransaction();
                $model->bill_id = $bill_id;
                \Yii::$app->warehouseService->billJ->returnGoods($model);
                $trans->commit();
                \Yii::$app->getSession()->setFlash('success', '保存成功');
                return ResultHelper::json(200, '保存成功');
            } catch (\Exception $e) {
                $trans->rollBack();
                return ResultHelper::json(422, $e->getMessage());
            }
        }
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => ['goodsJ' => ['lend_status']]
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['=', WarehouseBillJGoodsForm::tableName() . '.bill_id', $bill_id]);
        $dataProvider->query->andWhere(['in', WarehouseBillJGoodsForm::tableName() . '.id', $model->getIds()]);
        $dataProvider->query->andWhere(['in', 'goodsJ.lend_status', [LendStatusEnum::HAS_LEND, LendStatusEnum::PORTION_RETURN]]);
        $dataProvider->query->andWhere(['>', WarehouseBillJGoodsForm::tableName() . '.status', -1]);
        $model->qc_status = QcStatusEnum::PASS;
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $model,
        ]);
    }

    /**
     *
     * 批量编辑
     * @return mixed
     * @throws
     */
    public function actionEditAll()
    {
        $tab = Yii::$app->request->get('tab', 2);
        $returnUrl = Yii::$app->request->get('returnUrl', Url::to(['bill-j/edit-all']));
        $bill_id = Yii::$app->request->get('bill_id');
        $bill = WarehouseBillJForm::findOne($bill_id);
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'goodsJ' => ['lend_status', 'receive_id', 'receive_time', 'receive_remark', 'restore_time', 'qc_status', 'qc_remark',],
            ]
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['=', WarehouseBillJGoodsForm::tableName() . '.bill_id', $bill_id]);
        $dataProvider->query->andWhere(['>', WarehouseBillJGoodsForm::tableName() . '.status', -1]);
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'bill' => $bill,
            'tab' => $tab,
            'tabList' => \Yii::$app->warehouseService->bill->menuTabList($bill_id, $this->billType, $returnUrl),
        ]);
    }

    /**
     * ajax 更新商品明细
     *
     * @param $id
     * @return array
     */
    public function actionAjaxUpdate($id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return ResultHelper::json(404, '找不到数据');
        }
        $data = Yii::$app->request->get();
        $model->attributes = ArrayHelper::filter($data, array_keys($data));
        try {
            $trans = Yii::$app->trans->beginTransaction();
            if (!$model->save()) {
                return ResultHelper::json(422, $this->getError($model));
            }
            \Yii::$app->warehouseService->billJ->goodsJSummary($model->bill_id);
            $trans->commit();
            return ResultHelper::json(200, '修改成功');
        } catch (\Exception $e) {
            $trans->rollback();
            return ResultHelper::json(404, '找不到数据');
        }

    }

    /**
     *
     * 更改借货数量
     * @return array|mixed
     * @throws
     */
    public function actionAjaxLendNum()
    {
        $id = Yii::$app->request->get("id");
        $goods_num = Yii::$app->request->get("goods_num");
        if ($goods_num <= 0) {
            return ResultHelper::json(422, "借货数量必须大于0");
        }
        try {
            $trans = \Yii::$app->trans->beginTransaction();
            Yii::$app->warehouseService->billJ->updateLendNum($id, $goods_num);
            $trans->commit();
            return $this->message("操作成功", $this->redirect(\Yii::$app->request->referrer), 'success');
        } catch (\Exception $e) {
            $trans->rollBack();
            return ResultHelper::json(422, $e->getMessage());
        }
    }

    /**
     * 删除
     *
     * @param $id
     * @return mixed
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $billGoods = $this->findModel($id);
        $bill_id = $billGoods->bill_id;
        $bill = WarehouseBillJForm::findOne($bill_id);
        try {
            $trans = Yii::$app->db->beginTransaction();
            //删除明细关系表
            $goodJ = WarehouseBillGoodsJ::findOne($billGoods->id);
            //更新库存表商品状态为库存
            WarehouseGoods::updateAll(['goods_status' => GoodsStatusEnum::IN_STOCK], ['goods_id' => $billGoods->goods_id]);
            //还原库存
            \Yii::$app->warehouseService->warehouseGoods->updateStockNum($billGoods->goods_id, $billGoods->goods_num, AdjustTypeEnum::RESTORE);
            //删除
            $billGoods->delete();
            $goodJ->delete();
            //更新单据数量和金额
            $bill->goods_num = Yii::$app->warehouseService->bill->sumGoodsNum($bill_id);
            $bill->total_cost = Yii::$app->warehouseService->bill->sumCostPrice($bill_id);
            $bill->total_sale = Yii::$app->warehouseService->bill->sumSalePrice($bill_id);
            $bill->total_market = Yii::$app->warehouseService->bill->sumMarketPrice($bill_id);
            $bill->save();
            $trans->commit();
            return $this->message("删除成功", $this->redirect(['bill-j-goods/index', 'bill_id' => $bill_id]));
        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(['bill-j-goods/index', 'bill_id' => $bill_id]), 'error');
        }
    }

}
