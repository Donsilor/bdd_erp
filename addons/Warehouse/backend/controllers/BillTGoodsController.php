<?php

namespace addons\Warehouse\backend\controllers;

use addons\Warehouse\common\enums\BillStatusEnum;
use Yii;
use common\traits\Curd;
use common\helpers\Url;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseBillGoodsL;
use addons\Warehouse\common\forms\WarehouseBillTGoodsForm;
use addons\Warehouse\common\enums\BillTypeEnum;
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
        $total = $model->goodsSummary($bill_id, Yii::$app->request->queryParams);
        return $this->render($this->action->id, [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'bill' => $bill,
            'total' => $total,
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
                \Yii::$app->getSession()->setFlash('success', '保存成功');
                return $this->redirect(['edit-all', 'bill_id' => $bill_id]);
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
        $download = \Yii::$app->request->get('download',0);
        $bill = WarehouseBill::findOne($bill_id);
        if($download){
            $model = new WarehouseBillTGoodsForm();
            list($values, $fields) = $model->getTitleList();
            if(empty($bill_id)){
                header("Content-Disposition: attachment;filename=【".rand(100,999)."】入库单明细导入(".date('Ymd').").csv");
            }else{
                header("Content-Disposition: attachment;filename=【{$bill_id}】入库单明细导入($bill->bill_no).csv");
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
                $model->biaomiangongyi = join(',',$model->biaomiangongyi);


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
        $model->biaomiangongyi = explode(',',$model->biaomiangongyi);
        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }

    /**
     *
     * ajax批量填充
     * @return mixed|string|\yii\web\Response
     * @throws
     */
    public function actionBatchEdit()
    {
        $this->layout = '@backend/views/layouts/iframe';

        $ids = Yii::$app->request->post('ids');
        $ids = $ids ?? Yii::$app->request->get('ids');
        $model = new WarehouseBillTGoodsForm();
        $model->ids = $ids;
        $id_arr = $model->getIds();
        if (!$id_arr) {
            return ResultHelper::json(422, "ID不能为空");
        }
        $name = Yii::$app->request->post('name');
        $name = $name ?? Yii::$app->request->get('name');
        if (!$name) {
            return ResultHelper::json(422, "字段错误");
        }
        if (Yii::$app->request->isPost) {
            $value = Yii::$app->request->post('value');
            if (!$value) {
                return ResultHelper::json(422, "输入值不能为空");
            }
            try {
                $trans = Yii::$app->trans->beginTransaction();
                foreach ($id_arr as $id) {
                    $goods = WarehouseBillTGoodsForm::findOne(['id' => $id]);
                    $goods->$name = $value;
                    if (false === $goods->validate()) {
                        throw new \Exception($this->getError($goods));
                    }
                    if (false === $goods->save(true, [$name])) {
                        throw new \Exception($this->getError($goods));
                    }
                    $model->bill_id = $goods->bill_id;
                }
                \Yii::$app->warehouseService->billT->WarehouseBillTSummary($model->bill_id);
                $trans->commit();
                Yii::$app->getSession()->setFlash('success', '保存成功');
                return ResultHelper::json(200, '保存成功');
            } catch (\Exception $e) {
                $trans->rollBack();
                return ResultHelper::json(422, $e->getMessage());
            }
        }
        $attr_id = Yii::$app->request->get('attr_id', 0);
        if (!$attr_id) {
            return ResultHelper::json(422, '参数错误');
        }
        $style_arr = $model::find()->where(['id' => $id_arr])->select(['style_sn'])->asArray()->distinct('style_sn')->all();
        if (count($style_arr) != 1) {
            return ResultHelper::json(422, '请选择同款的商品进行操作');
        }
        $check = Yii::$app->request->get('check', null);
        if ($check) {
            return ResultHelper::json(200, '', ['url' => Url::to([$this->action->id, 'ids' => $ids, 'name' => $name, 'attr_id' => $attr_id])]);
        }
        $style_sn = $style_arr[0]['style_sn'] ?? "";
        $attr_arr = Yii::$app->styleService->styleAttribute->getAttrValueListByStyle($style_sn, $attr_id);
        return $this->render($this->action->id, [
            'model' => $model,
            'ids' => $ids,
            'name' => $name,
            'attr_arr' => $attr_arr
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
     * 删除/关闭
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

}
