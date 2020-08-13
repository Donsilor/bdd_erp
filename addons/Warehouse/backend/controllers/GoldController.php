<?php

namespace addons\Warehouse\backend\controllers;

use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseGold;
use addons\Warehouse\common\forms\WarehouseGoldForm;
use addons\Warehouse\common\forms\WarehouseGoldBillGoodsForm;
use addons\Warehouse\common\enums\GoldBillTypeEnum;
use addons\Supply\common\models\Supplier;
use common\helpers\StringHelper;
use common\helpers\ExcelHelper;
use common\helpers\PageHelper;
use common\helpers\Url;

/**
 * StyleChannelController implements the CRUD actions for StyleChannel model.
 */
class GoldController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseGoldForm::class;

    /**
     * Lists all StyleChannel models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['gold_name'], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'creator' => ['username'],
            ]
        ]);

        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams, ['created_at']);

        $created_at = $searchModel->created_at;
        if (!empty($updated_at)) {
            $dataProvider->query->andFilterWhere(['>=', WarehouseGold::tableName() . '.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<', WarehouseGold::tableName() . '.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)]);//结束时间
        }

        $dataProvider->query->andWhere(['>', WarehouseGold::tableName() . '.status', -1]);

        //导出
        if (Yii::$app->request->get('action') === 'export') {
            $this->actionExport($dataProvider);
        }

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
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
        $returnUrl = Yii::$app->request->get('returnUrl', Url::to(['gold/index']));
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseGoldForm();
        $bill = $model->getBillInfo();
        return $this->render($this->action->id, [
            'model' => $model,
            'tab' => $tab,
            'tabList' => \Yii::$app->warehouseService->gold->menuTabList($id, $returnUrl),
            'returnUrl' => $returnUrl,
            'bill' => $bill,
        ]);
    }

    /**
     * 领料信息
     * @return mixed
     */
    public function actionLingliao()
    {
        $this->modelClass = new WarehouseGoldBillGoodsForm();
        $tab = \Yii::$app->request->get('tab', 2);
        $returnUrl = \Yii::$app->request->get('returnUrl', Url::to(['gold/index']));
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'bill' => ['audit_time'],
            ]
        ]);
        $dataProvider = $searchModel
            ->search(Yii::$app->request->queryParams, ['created_at']);
        $created_at = $searchModel->created_at;
        if (!empty($created_at)) {
            $dataProvider->query->andFilterWhere(['>=', WarehouseGoldBillGoodsForm::tableName() . '.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<', WarehouseGoldBillGoodsForm::tableName() . '.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)]);//结束时间
        }
        $id = \Yii::$app->request->get('id');
        $gold = WarehouseGold::findOne(['id' => $id]);
        $dataProvider->query->andWhere(['=', 'gold_sn', $gold->gold_sn]);
        $dataProvider->query->andWhere(['>', WarehouseGoldBillGoodsForm::tableName() . '.status', -1]);

        $dataProvider->query->andWhere(['=', 'bill.bill_type', GoldBillTypeEnum::GOLD_C]);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'gold' => $gold,
            'tab' => $tab,
            'tabList' => \Yii::$app->warehouseService->gold->menuTabList($id, $returnUrl),
        ]);
    }

    /**
     * @param null $ids
     * @return bool|mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function actionExport($ids = null)
    {
        $name = '金料';
        if (!is_array($ids)) {
            $ids = StringHelper::explodeIds($ids);
        }
        if (!$ids) {
            return $this->message('ID不为空', $this->redirect(['index']), 'warning');
        }
        list($list,) = $this->getData($ids);
        $header = [
            ['批次号', 'gold_sn', 'text'],
            ['供应商', 'supplier_name', 'text'],
            ['金料类型', 'gold_type', 'text'],
            ['金料名称', 'gold_name', 'text'],
            ['金料款号', 'style_sn', 'text'],
            ['金料数量', 'gold_num', 'text'],
            ['库存重量(g)', 'gold_weight', 'text'],
            ['金料单价', 'gold_price', 'text'],
            ['备注', 'remark', 'text'],
        ];

        return ExcelHelper::exportData($list, $header, $name . '数据导出_' . date('YmdHis', time()));
    }


    private function getData($ids)
    {
        $select = ['g.*', 'sup.supplier_name'];
        $query = WarehouseGold::find()->alias('g')
            ->leftJoin(Supplier::tableName() . ' sup', 'sup.id=g.supplier_id')
            ->where(['g.id' => $ids])
            ->select($select);
        $lists = PageHelper::findAll($query, 100);
        //统计
        $total = [

        ];
        foreach ($lists as &$list) {
            $list['gold_type'] = \Yii::$app->attr->valueName($list['gold_type']);
        }
        return [$lists, $total];
    }

}
