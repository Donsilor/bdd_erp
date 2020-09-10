<?php

namespace addons\Finance\backend\controllers;

use addons\Supply\common\models\Supplier;
use Yii;
use common\traits\Curd;
use common\models\base\SearchModel;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Sales\common\models\SaleChannel;
use addons\Style\common\models\ProductType;
use addons\Sales\common\models\Payment;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\BillTypeEnum;
use common\helpers\ExcelHelper;
use common\helpers\PageHelper;
use common\helpers\StringHelper;

/**
 * Default controller for the `WarehouseBillGoods` module
 */
class FinanceEntryController extends BaseController
{
    use Curd;
    
    /**
     * @var WarehouseBillGoods
     */
    public $modelClass = WarehouseBillGoods::class;
    
    /**
     * Renders the index view for the module
     * @return string
     * @throws
     */
    public function actionIndex()
    {
        $searchModel = new SearchModel([
                'model' => $this->modelClass,
                'scenario' => 'default',
                'partialMatchAttributes' => [], // 模糊查询
                'defaultOrder' => [
                        'id' => SORT_DESC,
                ],
                'pageSize' => $this->pageSize,
                'relations' => [
                    'bill' => [
                        'id',
                        'channel_id',
                        'bill_status',
                        'created_at',
                        'audit_status',
                        'audit_time',
                        'status',
                    ],
                    'goods' => [
                        'gold_weight',
                        'gross_weight',
                        'gold_loss',
                        'gold_price',
                        'gold_amount',
                        'cost_price',
                        'diamond_carat',
                        'main_stone_price',
                        'second_stone_weight1',
                        'second_stone_price1',
                        'product_type_id',
                        'factory_cost',
                        'gong_fee',
                        'cert_fee',
                    ]
                ]
        ]);
        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, ['supplier_id', 'bill.audit_time']);
        $searchParams = Yii::$app->request->queryParams['SearchModel'] ?? [];

        $supplier_id = $searchModel->supplier_id;
        if($supplier_id){
            $dataProvider->query->andWhere(['=','bill.supplier_id', $supplier_id]);
        }
        //创建时间过滤
        if (!empty($searchParams['bill.audit_time'])) {
            list($start_date, $end_date) = explode('/', $searchParams['bill.audit_time']);
            $dataProvider->query->andFilterWhere(['between', 'bill.audit_time', strtotime($start_date), strtotime($end_date) + 86400]);
        }
        $dataProvider->query->andWhere(['in','bill.bill_type', [BillTypeEnum::BILL_TYPE_L, BillTypeEnum::BILL_TYPE_T]]);
        $dataProvider->query->andWhere(['=','bill.bill_status', BillStatusEnum::CONFIRM]);

        //导出
        if(\Yii::$app->request->get('action') === 'export'){
            $queryIds = $dataProvider->query->select(WarehouseBillGoods::tableName().'.id');
            $this->actionExport($queryIds);
        }

        return $this->render($this->action->id, [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
        ]);
    }

    /**
     * @param null $ids
     * @return bool|mixed
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function actionExport($ids = null){
        $name = '财务入库单';
        if(!is_array($ids)){
            $ids = StringHelper::explodeIds($ids);
        }
        if(!$ids){
            return $this->message('单据ID不为空', $this->redirect(['index']), 'warning');
        }
        list($list,) = $this->getData($ids);
        $header = [
            ['入库单号', 'bill_no', 'text'],
            ['入库时间', 'audit_time' , 'text'],
            ['供应商', 'supplier_name' , 'text'],
            ['商品名称', 'goods_name' , 'text'],
            ['产品线', 'product_type_name' , 'text'],
            ['货号', 'goods_id' , 'text'],
            ['总重', 'gross_weight' , 'text'],
            ['金损', 'gold_loss' , 'text'],
            ['金价', 'gold_price' , 'text'],
            ['金料额', 'gold_amount' , 'text'],
            ['主石重', 'diamond_carat' , 'text'],
            ['主石单价', 'main_stone_price' , 'text'],
            ['副石重', 'second_stone_weight1' , 'text'],
            ['副石单价', 'second_stone_price1' , 'text'],
            ['工费', 'gong_fee' , 'text'],
            ['证书费', 'cert_fee' , 'text'],
        ];
		if(\common\helpers\Auth::verify(\common\enums\SpecialAuthEnum::VIEW_CAIGOU_PRICE)){
			array_splice($header,0,0,['成本价', 'cost_price' , 'text']);
		}



        return ExcelHelper::exportData($list, $header, $name.'数据导出_' . date('YmdHis',time()));
    }

    private function getData($ids){
        $select = ['b.bill_no', 'b.audit_time', 'sup.supplier_name',
             'bg.goods_name', 'bg.style_sn', 'type.name as product_type_name',
            'bg.goods_id','g.gross_weight','g.gold_loss','g.gold_price','g.gold_amount', 'g.diamond_carat',
            'g.main_stone_price', 'g.second_stone_weight1', 'g.second_stone_price1', 'g.gong_fee',
            'g.cert_fee', 'g.cost_price', 'bg.sale_price'];
        $query = WarehouseBill::find()->alias('b')
            ->leftJoin(WarehouseBillGoods::tableName()." bg",'b.id=bg.bill_id')
            ->leftJoin(WarehouseGoods::tableName()." g",'bg.goods_id=g.goods_id')
            ->leftJoin(Supplier::tableName().' sup','sup.id=b.supplier_id')
            ->leftJoin(ProductType::tableName().' type','type.id=g.product_type_id')
            ->where(['b.bill_type' => [BillTypeEnum::BILL_TYPE_L, BillTypeEnum::BILL_TYPE_T], 'b.bill_status' => BillStatusEnum::CONFIRM])
            ->select($select);
        $lists = PageHelper::findAll($query, 100);
        //统计
        $total = [

        ];
        foreach ($lists as &$list){
            $list['audit_time'] = \Yii::$app->formatter->asDatetime($list['audit_time']) ?? "";
        }
        return [$lists,$total];
    }
}

