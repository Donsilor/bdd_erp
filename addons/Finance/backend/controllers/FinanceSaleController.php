<?php

namespace addons\Finance\backend\controllers;

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
class FinanceSaleController extends BaseController
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
                        'product_type_id',
                    ]
                ]
        ]);
        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, ['bill.audit_time']);
        $searchParams = Yii::$app->request->queryParams['SearchModel'] ?? [];

        //创建时间过滤
        if (!empty($searchParams['bill.audit_time'])) {
            list($start_date, $end_date) = explode('/', $searchParams['bill.audit_time']);
            $dataProvider->query->andFilterWhere(['between', 'bill.audit_time', strtotime($start_date), strtotime($end_date) + 86400]);
        }
        $dataProvider->query->andWhere(['in','bill.bill_type', [BillTypeEnum::BILL_TYPE_S, BillTypeEnum::BILL_TYPE_C]]);
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
        $name = '财务出库单';
        if(!is_array($ids)){
            $ids = StringHelper::explodeIds($ids);
        }
        if(!$ids){
            return $this->message('单据ID不为空', $this->redirect(['index']), 'warning');
        }
        list($list,) = $this->getData($ids);
        $header = [
            ['出库单号', 'bill_no', 'text'],
            ['出库时间', 'audit_time' , 'text'],
            ['销售渠道', 'channel_name' , 'text'],
            ['客户姓名', 'customer_name' , 'text'],
            ['商品名称', 'goods_name' , 'text'],
            ['产品线', 'product_type_name' , 'text'],
            ['货号', 'goods_id' , 'text'],
            ['实际销售价', 'sale_price' , 'text'],
            ['支付方式', 'pay_name' , 'text'],
            ['外部订单号', 'out_trade_no' , 'text'],
            ['销售人', 'sale_name' , 'text'],
        ];
		
		if(\common\helpers\Auth::verify(\common\enums\SpecialAuthEnum::VIEW_CAIGOU_PRICE)){
			array_splice($header,7,0,['成本价', 'cost_price' , 'text']);
		}

        return ExcelHelper::exportData($list, $header, $name.'数据导出_' . date('YmdHis',time()));
    }

    private function getData($ids){
        $select = ['b.bill_no', 'b.audit_time', 'sc.name as channel_name',
            'o.customer_name', 'bg.goods_name', 'type.name as product_type_name',
            'bg.goods_id', 'g.cost_price', 'bg.sale_price', 'pay.name as pay_name', 'o.out_trade_no', 'm.username as sale_name'];
        $query = WarehouseBill::find()->alias('b')
            ->leftJoin('bdd_erp.sales_order o','b.order_sn=o.order_sn')
            ->leftJoin(SaleChannel::tableName()." sc",'sc.id=b.channel_id')
            ->leftJoin('bdd_erp.member m','m.id=o.follower_id')
            ->leftJoin(WarehouseBillGoods::tableName()." bg",'b.id=bg.bill_id')
            ->leftJoin(WarehouseGoods::tableName()." g",'bg.goods_id=g.goods_id')
            ->leftJoin(ProductType::tableName().' type','type.id=g.product_type_id')
            ->leftJoin(Payment::tableName().' pay','pay.id=o.pay_type')
            ->where(['b.bill_type' => BillTypeEnum::BILL_TYPE_S, 'b.bill_status' => BillStatusEnum::CONFIRM])
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

