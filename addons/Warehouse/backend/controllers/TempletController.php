<?php

namespace addons\Warehouse\backend\controllers;


use addons\Warehouse\common\forms\WarehouseTempletForm;
use addons\Warehouse\common\models\WarehouseTemplet;
use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseGift;
use addons\Warehouse\common\forms\WarehouseGiftForm;
use addons\Sales\common\models\Order;
use addons\Sales\common\forms\OrderForm;
use addons\Sales\common\models\OrderGoods;
use addons\Supply\common\models\Supplier;
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
            ->search(Yii::$app->request->queryParams,['created_at']);
        $created_at = $searchModel->created_at;
        if (!empty($updated_at)) {
            $dataProvider->query->andFilterWhere(['>=',WarehouseTemplet::tableName().'.created_at', strtotime(explode('/', $created_at)[0])]);//起始时间
            $dataProvider->query->andFilterWhere(['<',WarehouseTemplet::tableName().'.created_at', (strtotime(explode('/', $created_at)[1]) + 86400)] );//结束时间
        }
        $dataProvider->query->andWhere(['>',WarehouseTemplet::tableName().'.status',-1]);
        //导出
        if(Yii::$app->request->get('action') === 'export'){
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
        $tab = Yii::$app->request->get('tab',1);
        $returnUrl = Yii::$app->request->get('returnUrl',Url::to(['templet/index', 'id'=>$id]));
        $model = $this->findModel($id);
        $model = $model ?? new WarehouseGiftForm();
        return $this->render($this->action->id, [
            'model' => $model,
            'tab'=>$tab,
            'tabList'=>\Yii::$app->warehouseService->templet->menuTabList($id, $returnUrl),
            'returnUrl'=>$returnUrl,
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
        $tab = Yii::$app->request->get('tab',1);
        $returnUrl = Yii::$app->request->get('returnUrl', Url::to(['index', 'id'=>$id]));
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
        $gift = WarehouseGift::findOne($id);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, ['created_at', 'order_time']);
        $searchParams = \Yii::$app->request->queryParams['SearchModel'] ?? [];
        $dataProvider->query->andWhere(['=', OrderGoods::tableName().'.style_sn', $gift->style_sn]);
        //创建时间过滤
        if (!empty($searchParams['order_time'])) {
            list($start_date, $end_date) = explode('/', $searchParams['order_time']);
            $dataProvider->query->andFilterWhere(['between', Order::tableName().'.order_time', strtotime($start_date), strtotime($end_date) + 86400]);
        }
        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'tab'=>$tab,
            'gift' => $gift,
            'tabList'=>\Yii::$app->warehouseService->gift->menuTabList($id, $returnUrl),
            'returnUrl'=>$returnUrl,
        ]);
    }

    /**
     * @param null $ids
     * @return bool|mixed
     * @throws
     */
    public function actionExport($ids = null){
        $name = '赠品';
        if(!is_array($ids)){
            $ids = StringHelper::explodeIds($ids);
        }
        if(!$ids){
            return $this->message('ID不为空', $this->redirect(['index']), 'warning');
        }
        list($list,) = $this->getData($ids);
        $header = [
            ['批次号', 'gift_sn' , 'text'],
            ['供应商', 'supplier_name' , 'text'],
            ['配件类型', 'gift_type' , 'text'],
            ['配件名称', 'gift_name' , 'text'],
            ['配件款号', 'style_sn' , 'text'],
            ['配件材质', 'material_type' , 'text'],
            ['配件形状', 'shape' , 'text'],
            ['配件颜色', 'color' , 'text'],
            ['链类型', 'chain_type' , 'text'],
            ['扣环', 'cramp_ring' , 'text'],
            ['尺寸', 'size' , 'text'],
            ['配件数量', 'gift_num' , 'text'],
            ['库存重量(g)', 'gift_weight' , 'text'],
            ['配件单价', 'gift_price' , 'text'],
            ['备注', 'remark' , 'text'],
        ];

        return ExcelHelper::exportData($list, $header, $name.'数据导出_' . date('YmdHis',time()));
    }

    private function getData($ids){
        $select = ['g.*','sup.supplier_name'];
        $query = WarehouseGift::find()->alias('g')
            ->leftJoin(Supplier::tableName().' sup','sup.id=g.supplier_id')
            ->where(['g.id' => $ids])
            ->select($select);
        $lists = PageHelper::findAll($query, 100);
        //统计
        $total = [

        ];
        foreach ($lists as &$list){
            $list['gift_type'] = \Yii::$app->attr->valueName($list['gift_type']);
            $list['material_type'] = \Yii::$app->attr->valueName($list['material_type']);
            $list['shape'] = \Yii::$app->attr->valueName($list['shape']);
            $list['color'] = \Yii::$app->attr->valueName($list['color']);
            $list['chain_type'] = \Yii::$app->attr->valueName($list['chain_type']);
            $list['cramp_ring'] = \Yii::$app->attr->valueName($list['cramp_ring']);
        }
        return [$lists,$total];
    }

}
