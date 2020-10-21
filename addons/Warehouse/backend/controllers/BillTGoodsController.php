<?php

namespace addons\Warehouse\backend\controllers;

use Yii;
use common\traits\Curd;
use common\helpers\Url;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseBillL;
use addons\Warehouse\common\models\WarehouseBillGoodsL;
use addons\Warehouse\common\forms\WarehouseBillPayForm;
use addons\Warehouse\common\forms\WarehouseBillTGoodsForm;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\BillTypeEnum;
use addons\Warehouse\common\enums\PayMethodEnum;
use addons\Warehouse\common\enums\PayTaxEnum;
use addons\Warehouse\common\enums\IsHiddenEnum;
use common\helpers\ArrayHelper;
use common\helpers\ResultHelper;
use yii\web\UploadedFile;

/**
 * WarehouseBillGoodsController implements the CRUD actions for WarehouseBillGoodsController model.
 */
class BillTGoodsController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseBillTGoodsForm::class;
    public $billType = BillTypeEnum::BILL_TYPE_T;

    /**
     * Lists all WarehouseBillGoods models.
     * @return mixed
     * @throws
     */
    public function actionIndex()
    {
        $bill_id = Yii::$app->request->get('bill_id');
        $tab = Yii::$app->request->get('tab', 2);
        $returnUrl = Yii::$app->request->get('returnUrl', Url::to(['bill-t-goods/index', 'bill_id' => $bill_id]));
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['goods_name', 'stone_remark', 'remark'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'productType' => ['name'],
                'styleCate' => ['name'],
            ]
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['=', 'bill_id', $bill_id]);
        $dataProvider->query->andWhere(['>', WarehouseBillGoodsL::tableName() . '.status', -1]);
        $bill = WarehouseBill::find()->where(['id' => $bill_id])->one();
        $model = new WarehouseBillTGoodsForm();
        $goods = $model::find()->select(['goods_id'])->where(['bill_id' => $bill_id])->all();
        $goods_ids = $model->getCopyGoodsIds($goods);
        $total = $model->goodsSummary($bill_id, Yii::$app->request->queryParams);
        return $this->render($this->action->id, [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'bill' => $bill,
            'total' => $total,
            'goods_ids' => $goods_ids ?? "",
            'tabList' => \Yii::$app->warehouseService->bill->menuTabList($bill_id, $this->billType, $returnUrl),
            'tab' => $tab,
        ]);
    }

    /**
     *
     * ajax添加商品
     * @return mixed|string|\yii\web\Response
     * @throws
     */
    public function actionAjaxEdit()
    {
        $id = \Yii::$app->request->get('id');
        $bill_id = Yii::$app->request->get('bill_id');
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseBillTGoodsForm();
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            try {
                $trans = \Yii::$app->db->beginTransaction();
                $model->bill_id = $bill_id;
                Yii::$app->warehouseService->billT->addBillTGoods($model);
                $trans->commit();
                return $this->message("保存成功", $this->redirect(['index', 'bill_id' => $bill_id]), 'success');
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
     *
     * ajax查看图片
     * @return mixed|string|\yii\web\Response
     * @throws
     */
    public function actionAjaxImage()
    {
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseBillTGoodsForm();
        return $this->renderAjax($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     *
     * ajax批量导入
     * @return mixed|string|\yii\web\Response
     * @throws
     */
    public function actionAjaxUpload()
    {
        $id = \Yii::$app->request->get('id');
        $bill_id = \Yii::$app->request->get('bill_id');
        $download = \Yii::$app->request->get('download', 0);
        $type = \Yii::$app->request->get('download_type', 0);
        $bill = WarehouseBill::findOne($bill_id);
        if ($download) {
            $model = new WarehouseBillTGoodsForm();
            list($values, $fields) = $model->getTitleList($type);
            if (empty($bill_id)) {
                header("Content-Disposition: attachment;filename=【" . rand(100, 999) . "】" . ($type ? "通用" : "素金") . "-其他入库单导入模板(" . date('Ymd') . ").csv");
            } else {
                header("Content-Disposition: attachment;filename=【{$bill_id}】" . ($type ? "通用" : "素金") . "-其他入库单导入模板($bill->bill_no).csv");
            }
            $content = implode($values, ",") . "\n" . implode($fields, ",") . "\n";
            echo iconv("utf-8", "gbk", $content);
            exit();
        }
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseBillTGoodsForm();
        // ajax 校验
        $this->activeFormValidate($model);
        if (Yii::$app->request->isPost) {
            try {
                $trans = \Yii::$app->db->beginTransaction();
                $model->bill_id = $bill_id;
                $model->file = UploadedFile::getInstance($model, 'file');
                \Yii::$app->warehouseService->billT->uploadGoods($model);
                $trans->commit();
                \Yii::$app->getSession()->setFlash('success', '保存成功');
                return $this->redirect(['index', 'bill_id' => $bill_id]);
            } catch (\Exception $e) {
                $trans->rollBack();
                //var_dump($e->getTraceAsString());die;
                return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
            }
        }
        return $this->renderAjax($this->action->id, [
            'model' => $model,
            'bill' => $bill,
        ]);
    }

    /**
     *
     * ajax编辑
     * @return mixed|string|\yii\web\Response
     * @throws
     */
    public function actionEdit()
    {
        $this->layout = '@backend/views/layouts/iframe';

        $id = \Yii::$app->request->get('id');
        //$bill_id = Yii::$app->request->get('bill_id');
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseBillTGoodsForm();
        // ajax 校验
        //$this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            try {
                $trans = \Yii::$app->db->beginTransaction();
                //$model->biaomiangongyi = join(',',$model->biaomiangongyi);
                $result = $model->updateFromValidate($model);
                if ($result['error'] == false) {
                    throw new \Exception($result['msg']);
                }
                //list($model,) = $model->correctGoods($model);
                if (false === $model->save()) {
                    throw new \Exception($this->getError($model));
                }
                \Yii::$app->warehouseService->billT->syncUpdatePrice($model);
                \Yii::$app->warehouseService->billT->WarehouseBillTSummary($model->bill_id);
                $trans->commit();
                Yii::$app->getSession()->setFlash('success', '保存成功');
                return ResultHelper::json(200, '保存成功');
            } catch (\Exception $e) {
                $trans->rollBack();
                return ResultHelper::json(422, $e->getMessage());
            }
        }
        $model->biaomiangongyi = explode(',', $model->biaomiangongyi);
        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     *
     * 查看
     * @return mixed|string|\yii\web\Response
     * @throws
     */
    public function actionShow()
    {
        $this->layout = '@backend/views/layouts/iframe';
        $id = \Yii::$app->request->get('id');
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseBillTGoodsForm();
        $model->biaomiangongyi = explode(',', $model->biaomiangongyi);
        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     * ajax更新排序/状态
     *
     * @param $id
     * @return array
     */
    public function actionAjaxUpdate($id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return ResultHelper::json(404, '找不到数据');
        }
        $params = Yii::$app->request->get();
        $keys = array_keys($params);  //$model->attributes();
        try {
            $trans = \Yii::$app->db->beginTransaction();
            $model->attributes = ArrayHelper::filter($params, $keys);
            $result = $model->updateFromValidate($model);
            if ($result['error'] == false) {
                throw new \Exception($result['msg']);
            }
            list($model,) = $model->correctGoods($model);
            if (!$model->save()) {
                throw new \Exception("保存失败");
            }
            \Yii::$app->warehouseService->billT->syncUpdatePrice($model);
            \Yii::$app->warehouseService->billT->WarehouseBillTSummary($model->bill_id);
            $trans->commit();
            return ResultHelper::json(200, '修改成功');
        } catch (\Exception $e) {
            $trans->rollBack();
            return ResultHelper::json(422, $e->getMessage());
        }
    }

    /**
     *
     * ajax批量填充
     * @return mixed|string|
     * @throws
     */
    public function actionBatchEdit()
    {
        $this->layout = '@backend/views/layouts/iframe';

        $model = new WarehouseBillTGoodsForm();
        $model->ids = \Yii::$app->request->get('ids', null);
        $model->ids = $model->ids ?? \Yii::$app->request->post('ids', null);
        if (!$model->ids) {
            return ResultHelper::json(422, "ID不能为空");
        }
        $model->batch_name = \Yii::$app->request->get('name', null);
        $model->batch_name = $model->batch_name ?? \Yii::$app->request->post('name', null);
        if (!$model->batch_name) {
            return ResultHelper::json(422, "字段名称不能为空");
        }
        $model->attr_id = \Yii::$app->request->get('attr_id', null);
        $model->attr_id = $model->attr_id ?? \Yii::$app->request->post('attr_id', null);
        $check = \Yii::$app->request->get('check', null);
        if ($check) {
            return ResultHelper::json(200, '', ['url' => Url::to([$this->action->id, 'ids' => $model->ids, 'name' => $model->batch_name, 'attr_id' => $model->attr_id])]);
        }
        if (\Yii::$app->request->isPost) {
            try {
                $trans = \Yii::$app->trans->beginTransaction();
                $post = \Yii::$app->request->post('WarehouseBillTGoodsForm', null);
                $model->batch_value = $post['batch_value'] ?? \Yii::$app->request->post('value', null);
                if (!$model->batch_value) {
                    throw new \Exception("输入值不能为空");
                }
                \Yii::$app->warehouseService->billT->batchEdit($model);
                $trans->commit();
                \Yii::$app->getSession()->setFlash('success', '保存成功');
                return ResultHelper::json(200, '保存成功');//['url'=>Url::to(['edit-all', 'bill_id' => $model->bill_id])."#suttle_weight"]
            } catch (\Exception $e) {
                $trans->rollBack();
                return ResultHelper::json(422, $e->getMessage());
            }
        }
        $model->attr_list = $model->getBatchSelectMap(null, $model->attr_id, $model->batch_name);
        return $this->render($this->action->id, [
            'model' => $model,
        ]);

    }

    /**
     *
     * 收货单-批量编辑
     * @return mixed
     * @throws
     */
    public function actionEditAll()
    {
        $bill_id = Yii::$app->request->get('bill_id');
        $tab = Yii::$app->request->get('tab', 3);
        $returnUrl = Yii::$app->request->get('returnUrl', Url::to(['bill-t-goods/index', 'bill_id' => $bill_id]));
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['goods_name', 'stone_remark', 'biaomiangongyi', 'remark'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => []
        ]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['=', 'bill_id', $bill_id]);
        //$dataProvider->query->andWhere(['>',WarehouseBillGoodsT::tableName().'.status',-1]);
        $bill = WarehouseBill::find()->where(['id' => $bill_id])->one();
        if ($bill->bill_status != BillStatusEnum::SAVE) {
            exit("单据不是保存状态");
        }
        $model = new WarehouseBillTGoodsForm();
        $total = $model->goodsSummary($bill_id);
        return $this->render($this->action->id, [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'bill' => $bill,
            'total' => $total,
            'tabList' => \Yii::$app->warehouseService->bill->menuTabList($bill_id, $this->billType, $returnUrl, $tab),
            'tab' => $tab,
        ]);
    }

    /**
     *
     * 删除
     * @param $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!($model = $this->modelClass::findOne($id))) {
            return $this->message("找不到数据", $this->redirect(['index']), 'error');
        }
        try {
            $trans = \Yii::$app->db->beginTransaction();
            if (false === $model->delete()) {
                throw new \Exception($this->getError($model));
            }
            //更新收货单汇总：总金额和总数量
            $res = \Yii::$app->warehouseService->billT->WarehouseBillTSummary($model->bill_id);
            if (false === $res) {
                throw new \yii\db\Exception('更新单据汇总失败');
            }
            $trans->commit();
            \Yii::$app->getSession()->setFlash('success', '删除成功');
            return $this->redirect(\Yii::$app->request->referrer);
        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
    }

    /**
     *
     * 批量删除
     * @return mixed
     */
    public function actionBatchDelete()
    {
        $ids = Yii::$app->request->post('ids');
        if (empty($ids)) {
            return $this->message("ID不能为空", $this->redirect(['index']), 'error');
        }
        foreach ($ids as $id) {
            if (!($model = $this->modelClass::findOne($id))) {
                return $this->message("找不到数据", $this->redirect(['index']), 'error');
            }
        }
        try {
            $trans = \Yii::$app->db->beginTransaction();
            WarehouseBillTGoodsForm::deleteAll(['id' => $ids]);
            \Yii::$app->warehouseService->billT->WarehouseBillTSummary($model->bill_id);
            $trans->commit();
            \Yii::$app->getSession()->setFlash('success', '删除成功');
            return $this->redirect(\Yii::$app->request->referrer);
        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
    }

    /**
     *
     * 同步更新价格
     * @return mixed
     */
    public function actionUpdatePrice()
    {
        $ids = Yii::$app->request->post('ids');
        if (empty($ids)) {
            return $this->message("ID不能为空", $this->redirect(['index']), 'error');
        }
        try {
            $trans = \Yii::$app->db->beginTransaction();
            foreach ($ids as $id) {
                $model = WarehouseBillTGoodsForm::findOne($id);
                if (!empty($model)) {
                    \Yii::$app->warehouseService->billT->syncUpdatePrice($model);
                }
            }
            \Yii::$app->warehouseService->billT->WarehouseBillTSummary($model->bill_id);
            $trans->commit();
            \Yii::$app->getSession()->setFlash('success', '刷新成功');
            return $this->redirect(\Yii::$app->request->referrer);
        } catch (\Exception $e) {
            $trans->rollBack();
            return $this->message($e->getMessage(), $this->redirect(\Yii::$app->request->referrer), 'error');
        }
    }

    /**
     * 创建结算信息
     * @return mixed
     * @throws
     */
    public function actionCreatePay()
    {
        $this->layout = '@backend/views/layouts/iframe';

        $ids = Yii::$app->request->get('ids');
        $bill_id = Yii::$app->request->get('bill_id');
        $check = Yii::$app->request->get('check', null);
        $model = new WarehouseBillPayForm();
        $billM = WarehouseBill::findOne($bill_id);
        $model->supplier_id = $billM->supplier_id;
        $model->ids = $ids;
        if ($check) {
            try {
                \Yii::$app->warehouseService->billPay->billPayValidate($model);
                return ResultHelper::json(200, '', ['url' => Url::to([$this->action->id, 'bill_id' => $bill_id, 'ids' => $ids])]);
            } catch (\Exception $e) {
                return ResultHelper::json(422, $e->getMessage());
            }
        }
        if ($model->load(Yii::$app->request->post())) {
            try {
                $trans = Yii::$app->trans->beginTransaction();

                \Yii::$app->warehouseService->billPay->createBillPay($model);
                $trans->commit();
                Yii::$app->getSession()->setFlash('success', '保存成功');
                return ResultHelper::json(200, '保存成功');
            } catch (\Exception $e) {
                $trans->rollBack();
                return ResultHelper::json(422, $e->getMessage());
            }
        }
        $total = \Yii::$app->warehouseService->billPay->calcPaySummary($model);
        //$model->pay_content = PayContentEnum::FACTORY_COST;
        $model->pay_method = PayMethodEnum::TALLY;
        $model->pay_tax = PayTaxEnum::YES_TAX;
        return $this->render($this->action->id, [
            'model' => $model,
            'total' => $total,
        ]);
    }

    /**
     * ajax显示/隐藏
     *
     * @param $id
     * @return array
     */
    public function actionAjaxHidden($id)
    {
        $this->modelClass = WarehouseBillL::class;
        if (!$id) {
            return ResultHelper::json(404, 'ID不能为空');
        }
        if (!($model = $this->modelClass::findOne($id))) {
            $billT = WarehouseBillL::findOne($id);
            $billT = $billT ?? new WarehouseBillL();
            $billT->id = $id;
            if (false === $billT->save()) {
                return ResultHelper::json(404, '找不到数据');
            }
        }
        $params = Yii::$app->request->get();
        if (!$params['name'] || $params['value'] === "") {
            return ResultHelper::json(404, '参数值不能为空');
        }
        $name = $params['name'] ?? "";
        $value = $params['value'] ?? 0;
        if ($name == 'show_all') {
            $save = [
                'show_all' => $value,
                'show_basic' => IsHiddenEnum::NO,
                'show_attr' => $value,
                'show_gold' => $value,
                'show_main_stone' => $value,
                'show_second_stone1' => $value,
                'show_second_stone2' => $value,
                'show_second_stone3' => $value,
                'show_parts' => $value,
                'show_fee' => $value,
                'show_price' => $value,
            ];
        } else {
            $save[$name] = $value;
        }
        try {
            $trans = \Yii::$app->db->beginTransaction();
            $model->attributes = $save;
            if (!$model->save()) {
                throw new \Exception("保存失败");
            }
            $trans->commit();
            return ResultHelper::json(200, '修改成功');
        } catch (\Exception $e) {
            $trans->rollBack();
            return ResultHelper::json(422, $e->getMessage());
        }
    }

}
