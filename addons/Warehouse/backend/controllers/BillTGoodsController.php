<?php

namespace addons\Warehouse\backend\controllers;

use Yii;
use common\traits\Curd;
use common\helpers\Url;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseBillGoodsL;
use addons\Warehouse\common\forms\WarehouseBillTGoodsForm;
use addons\Warehouse\common\enums\BillTypeEnum;
use common\helpers\ResultHelper;
use yii\base\Exception;
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
            'partialMatchAttributes' => [], // 模糊查询
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
        return $this->render($this->action->id, [
            'model' => new WarehouseBillTGoodsForm(),
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'bill' => $bill,
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
     * 文件格式导出
     * @return mixed|string|\yii\web\Response
     * @throws
     */
    public function actionDownload()
    {
        $bill_id = \Yii::$app->request->get('bill_id');
        $bill = WarehouseBill::findOne($bill_id);
        $fields = [
            '条码号(货号)', '款号', '起版号', '商品名称', '材质', '材质颜色', '手寸(港号)', '手寸(美号)', '尺寸(cm)', '成品尺寸(cm)', '镶口(ct)', '刻字', '链类型', '扣环', '爪头形状',
            '配料方式(工厂配/公司配)', '连石重(g)', '金重(g)', '损耗(%)', '金价/g',
            '主石配石方式(工厂配/公司配)', '主石编号', '主石类型', '主石粒数', '主石重(ct)', '主石单价/ct', '主石形状', '主石颜色', '主石净度', '主石切工', '主石色彩', '主石规格',
            '副石1配石方式(工厂配/公司配)', '副石1编号', '副石1类型', '副石1粒数', '副石1重(ct)', '副石1单价/ct', '副石1形状', '副石1颜色', '副石1净度', '副石1色彩',
            '副石2配石方式(工厂配/公司配)', '副石2编号', '副石2类型', '副石2粒数', '副石2重(ct)', '副石2单价/ct', '副石2形状', '副石2规格', '石料备注',
            '配件方式(工厂配/公司配)', '配件类型', '配件材质', '配件数量', '配件金重(g)', '配件金价/g',
            '配石数量', '配石重量(ct)', '配石工费/ct', '配件工费', '克/工费', '镶嵌工艺', '镶石单价/ct', '表面工艺', '表面工艺费', '分色/分件费', '喷拉砂费', '补口费', '版费', '证书费',
            '其他费用', '主石证书号', '主石证书类型', '倍率(默认1)', '金托类型(成品/空托)', '备注',
        ];
        header("Content-Disposition: attachment;filename=".mt_rand(100,999)."入库单明细($bill->bill_no).csv");
        echo iconv("utf-8", "gbk", implode($fields, ",") . "\n");
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
        $bill_id = Yii::$app->request->get('bill_id');
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseBillTGoodsForm();
        $bill = WarehouseBill::findOne($bill_id);
        // ajax 校验
        $this->activeFormValidate($model);
        if (Yii::$app->request->isPost) {
            try {
                $trans = \Yii::$app->db->beginTransaction();
                $model->bill_id = $bill_id;
                $model->file = UploadedFile::getInstance($model, 'file');
                Yii::$app->warehouseService->billT->uploadGoods($model);
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
        $model = $model ?? new WarehouseBillGoodsL();
        // ajax 校验
        //$this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            try {
                $trans = \Yii::$app->db->beginTransaction();

                \Yii::$app->warehouseService->billT->syncUpdatePrice($model);
                \Yii::$app->warehouseService->billT->WarehouseBillTSummary($model->bill_id);
//                if (false === $model->save()) {
//                    throw new \Exception($this->getError($model));
//                }
                $trans->commit();
                Yii::$app->getSession()->setFlash('success', '保存成功');
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
     *
     * ajax批量编辑
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
        $check = Yii::$app->request->get('check', null);
        if ($check) {
            return ResultHelper::json(200, '', ['url' => Url::to([$this->action->id, 'ids' => $ids, 'name' => $name, 'attr_id' => $attr_id])]);
        }
        $style_arr = $model::find()->where(['id' => $id_arr])->select(['style_sn'])->asArray()->distinct('style_sn')->all();
        if (count($style_arr) != 1) {
            return ResultHelper::json(422, '请选择同款的商品进行操作');
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
     * 收货单-编辑
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
            'partialMatchAttributes' => [], // 模糊查询
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
        return $this->render($this->action->id, [
            'model' => new WarehouseBillTGoodsForm(),
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'bill' => $bill,
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
