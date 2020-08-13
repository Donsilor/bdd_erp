<?php

namespace addons\Warehouse\backend\controllers;

use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseTemplet;
use addons\Warehouse\common\forms\WarehouseTempletForm;
use addons\Sales\common\models\Order;
use addons\Sales\common\models\OrderGoods;
use addons\Sales\common\forms\OrderForm;
use addons\Supply\common\models\Supplier;
use addons\Warehouse\common\enums\LayoutTypeEnum;
use common\helpers\StringHelper;
use common\helpers\ExcelHelper;
use common\helpers\PageHelper;
use common\helpers\Url;

/**
 * 样板库存
 */
class TempletController extends BaseController
{
    use Curd;
    public $modelClass = WarehouseTempletForm::class;

    /**
     * 列表
     * @return mixed
     * @throws
     */
    public function actionIndex()
    {
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => ['gift_name'], // 模糊查询
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
            $dataProvider->query->andFilterWhere(['>=', WarehouseTemplet::tableName() . '.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<', WarehouseTemplet::tableName() . '.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)]);//结束时间
        }
        $dataProvider->query->andWhere(['>', WarehouseTemplet::tableName() . '.status', -1]);
        //导出
        if (Yii::$app->request->get('action') === 'export') {
            $queryIds = $dataProvider->query->select(WarehouseTemplet::tableName() . '.id');
            $this->actionExport($queryIds);
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
        $returnUrl = Yii::$app->request->get('returnUrl', Url::to(['templet/index', 'id' => $id]));
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseTempletForm();
        return $this->render($this->action->id, [
            'model' => $model,
            'tab' => $tab,
            'tabList' => \Yii::$app->warehouseService->templet->menuTabList($id, $returnUrl),
            'returnUrl' => $returnUrl,
        ]);
    }

    /**
     * 客户订单列表
     * @return string
     * @throws
     */
    public function actionOrder()
    {
        $this->modelClass = OrderForm::class;
        $id = Yii::$app->request->get('id');
        $tab = Yii::$app->request->get('tab', 1);
        $returnUrl = Yii::$app->request->get('returnUrl', Url::to(['index', 'id' => $id]));
        $searchModel = new SearchModel([
            'model' => $this->modelClass,
            'scenario' => 'default',
            'partialMatchAttributes' => [], // 模糊查询
            'defaultOrder' => [
                'id' => SORT_DESC,
            ],
            'pageSize' => $this->pageSize,
            'relations' => [
                'goods' => ['style_sn'],
                'account' => ['order_amount', 'refund_amount'],
            ]
        ]);
        $templet = WarehouseTemplet::findOne($id);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, ['created_at', 'order_time']);
        $searchParams = \Yii::$app->request->queryParams['SearchModel'] ?? [];
        $dataProvider->query->andWhere(['=', OrderGoods::tableName() . '.style_sn', $templet->style_sn]);
        //创建时间过滤
        if (!empty($searchParams['order_time'])) {
            list($start_date, $end_date) = explode('/', $searchParams['order_time']);
            $dataProvider->query->andFilterWhere(['between', Order::tableName() . '.order_time', strtotime($start_date), strtotime($end_date) + 86400]);
        }
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'tab' => $tab,
            'templet' => $templet,
            'tabList' => \Yii::$app->warehouseService->templet->menuTabList($id, $returnUrl),
            'returnUrl' => $returnUrl,
        ]);
    }

    /**
     * @param null $ids
     * @return bool|mixed
     * @throws
     */
    public function actionExport($ids = null)
    {
        $name = '样板';
        if (!is_array($ids)) {
            $ids = StringHelper::explodeIds($ids);
        }
        if (!$ids) {
            return $this->message('ID不为空', $this->redirect(['index']), 'warning');
        }
        list($list,) = $this->getData($ids);
        $header = [
            ['批次号', 'gift_sn', 'text'],
            ['供应商', 'supplier_name', 'text'],
            ['版式类型', 'layout_type', 'text'],
            ['样板名称', 'goods_name', 'text'],
            ['样板款号', 'style_sn', 'text'],
            ['起版号', 'qiban_sn', 'text'],
            ['手寸(美号)', 'finger', 'text'],
            ['手寸(港号)', 'finger_hk', 'text'],
            ['样板数量', 'goods_num', 'text'],
            ['净重(g)', 'suttle_weight', 'text'],
            ['成品尺寸(mm)', 'goods_size', 'text'],
            ['总石重(ct)', 'stone_weight', 'text'],
            ['石头规格', 'stone_size', 'text'],
            ['成本价', 'cost_price', 'text'],
            ['备注', 'remark', 'text'],
        ];

        return ExcelHelper::exportData($list, $header, $name . '数据导出_' . date('YmdHis', time()));
    }

    private function getData($ids)
    {
        $select = ['g.*', 'sup.supplier_name'];
        $query = WarehouseTemplet::find()->alias('g')
            ->leftJoin(Supplier::tableName() . ' sup', 'sup.id=g.supplier_id')
            //->where(['g.id' => $ids])
            ->select($select);
        $lists = PageHelper::findAll($query, 100);
        //统计
        $total = [

        ];
        foreach ($lists as &$list) {
            $list['finger'] = \Yii::$app->attr->valueName($list['finger']);
            $list['finger_hk'] = \Yii::$app->attr->valueName($list['finger_hk']);
            $list['layout_type'] = LayoutTypeEnum::getValue($list['layout_type']);
        }
        return [$lists, $total];
    }

}
