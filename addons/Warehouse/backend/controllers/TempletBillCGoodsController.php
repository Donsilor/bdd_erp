<?php

namespace addons\Warehouse\backend\controllers;

use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseTempletBill;
use addons\Warehouse\common\models\WarehouseTempletBillGoods;
use addons\Warehouse\common\forms\WarehouseTempletBillCGoodsForm;
use addons\Warehouse\common\enums\TempletBillTypeEnum;
use common\helpers\ExcelHelper;
use common\helpers\Url;

/**
 * 样板出库单
 */
class TempletBillCGoodsController extends TempletBillGoodsController
{
    use Curd;
    public $modelClass = WarehouseTempletBillCGoodsForm::class;
    public $billType = TempletBillTypeEnum::TEMPLET_C;

    /**
     * 列表
     * @return mixed
     */
    public function actionIndex()
    {
        $bill_id = \Yii::$app->request->get('bill_id');
        $tab = \Yii::$app->request->get('tab', 2);
        $returnUrl = \Yii::$app->request->get('returnUrl', Url::to(['templet-bill-c-goods/index', 'bill_id' => $bill_id]));
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [

            ]
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams, ['created_at']);

        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=', WarehouseTempletBillGoods::tableName() . '.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<', WarehouseTempletBillGoods::tableName() . '.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)]);//结束时间
        }

        $dataProvider->query->andWhere(['=', 'bill_id', $bill_id]);

        $batch_sn = \Yii::$app->request->get('batch_sn', null);
        if ($batch_sn) {
            $dataProvider->query->andWhere(['=', 'batch_sn', $batch_sn]);
        }
        $dataProvider->query->andWhere(['>', WarehouseTempletBillGoods::tableName() . '.status', -1]);
        //导出
        if (Yii::$app->request->get('action') === 'export') {
            $this->getExport($dataProvider);
        }
        $bill = WarehouseTempletBill::find()->where(['id' => $bill_id])->one();
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'bill' => $bill,
            'tab' => $tab,
            'tabList' => \Yii::$app->warehouseService->templetBill->menuTabList($bill_id, $this->billType, $returnUrl),
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
        $bill_id = \Yii::$app->request->get('bill_id');
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseTempletBillGoods();
        // ajax 校验
        $this->activeFormValidate($model);
        if ($model->load(\Yii::$app->request->post())) {
            try {
                $trans = \Yii::$app->db->beginTransaction();
                $model->bill_id = $bill_id;
                \Yii::$app->warehouseService->templetC->createBillGoods($model);
                $trans->commit();
                return $this->message('保存成功', $this->redirect(Yii::$app->request->referrer), 'success');
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
     * 编辑明细
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionEditAll()
    {
        $bill_id = Yii::$app->request->get('bill_id');
        $tab = Yii::$app->request->get('tab', 3);
        $returnUrl = Yii::$app->request->get('returnUrl', Url::to(['templet-bill-c-goods/index', 'bill_id' => $bill_id]));
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [

            ]
        ]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->query->andWhere(['=', 'bill_id', $bill_id]);
        $dataProvider->query->andWhere(['>', WarehouseTempletBillGoods::tableName() . '.status', -1]);

        $bill = WarehouseTempletBill::find()->where(['id' => $bill_id])->one();
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'bill' => $bill,
            'tabList' => \Yii::$app->warehouseService->templetBill->menuTabList($bill_id, $this->billType, $returnUrl, $tab),
            'returnUrl' => $returnUrl,
            'tab' => $tab,
        ]);
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
        $purchase_id = Yii::$app->request->get('purchase_id');
        try {
            $trans = Yii::$app->trans->beginTransaction();

            $model = $this->findModel($id) ?? new WarehouseTempletBillGoods();
            if (!$model->delete()) {
                throw new \Exception("删除失败", 422);
            }
            //更新单据汇总
            Yii::$app->warehouseService->templetBill->BillSummary($purchase_id);
            $trans->commit();
            return $this->message('删除成功', $this->redirect(Yii::$app->request->referrer), 'success');
        } catch (\Exception $e) {
            $trans->rollback();
            return $this->message($e->getMessage(), $this->redirect(Yii::$app->request->referrer), 'error');
        }
    }

    public function getExport($dataProvider)
    {
        $list = $dataProvider->models;
        $header = [
            ['ID', 'id'],
            ['渠道名称', 'name', 'text'],
        ];
        return ExcelHelper::exportData($list, $header, '数据导出_' . time());

    }

}
