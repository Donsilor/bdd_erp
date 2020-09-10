<?php

namespace addons\Sales\services;

use Yii;
use addons\Sales\common\enums\DistributeStatusEnum;
use addons\Sales\common\models\OrderGoods;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\BillTypeEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\enums\OrderTypeEnum;
use addons\Warehouse\common\models\WarehouseGoods;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;
use common\components\Service;
use common\helpers\Url;
use yii\db\Exception;

/**
 * Class DistributionOrderService
 * @package services\common
 */
class DistributionOrderService extends Service
{
    /**
     * tab
     * @param int $order_id
     * @param string $returnUrl
     * @return array
     */
    public function menuTabList($order_id, $returnUrl = null)
    {
        return [
            1=>['name'=>'待配货详情','url'=>Url::to(['order/account-sales','id'=>$order_id,'tab'=>1,'returnUrl'=>$returnUrl])],
            //2=>['name'=>'日志信息','url'=>Url::to(['order-log/index','order_id'=>$order_id,'tab'=>2,'returnUrl'=>$returnUrl])],
        ];
    }

    /**
     * 销账
     * @param object $form
     * @return array
     * @throws
     */
    public function AccountSales($form)
    {
        if(!$form->goods_ids){
            throw new \Exception("请填写需要销账的货号");
        }
        $bill_goods = [];
        $total_cost = $total_sale = $total_market = 0;
        foreach ($form->goods_ids as $id => $goods_id) {
            if(!$goods_id){
                throw new \Exception("货号不能为空");
            }

            $goods = WarehouseGoods::find()->where(['goods_id'=>$goods_id])->one();
            if(!$goods){
                throw new \Exception("货号".$goods_id."不存在");
            }
            //if($goods->goods_status != GoodsStatusEnum::IN_STOCK){
                //throw new \Exception("货号".$goods_id."不是库存状态");
            //}
            //if($goods->order_sn && $goods->order_detail_id){
                //throw new \Exception("货号".$goods_id."已经被订单".$goods->order_sn."绑定");
            //}
            $orderGoods = OrderGoods::findOne($id);
            //$goodsAccount = OrderAccount::findOne($id);
            //$goods = new WarehouseGoods();
            $bill_goods[] = [
                'goods_id' => $goods_id,
                'goods_name' => $goods->goods_name,
                'style_sn' => $goods->style_sn,
                'goods_num' => $goods->goods_num,
                'order_detail_id' => $id,
                'source_detail_id' => $id,
                'put_in_type' => $goods->put_in_type,
                'warehouse_id' => $goods->warehouse_id,
                'material' => $goods->material,
                'material_type' => $goods->material_type,
                'material_color' => $goods->material_color,
                'gold_weight' => $goods->gold_weight,
                'gold_loss' => $goods->gold_loss,
                'diamond_carat' => $goods->diamond_carat,
                'diamond_color' => $goods->diamond_color,
                'diamond_clarity' => $goods->diamond_clarity,
                'diamond_cert_id' => $goods->diamond_cert_id,
                'diamond_cert_type' => $goods->diamond_cert_type,
                'cost_price' => $goods->cost_price,
                'sale_price' => $orderGoods->goods_pay_price,
                'market_price' => $goods->market_price,
                'markup_rate' => 1,
                'status' => StatusEnum::ENABLED,
                'creator_id' =>\Yii::$app->user->identity->getId(),
                'created_at' => time(),
            ];

            $total_cost = bcadd($total_cost, $goods->cost_price, 2);
            $total_market = bcadd($total_market, $goods->market_price, 2);
            $total_sale = bcadd($total_sale, $orderGoods->goods_pay_price, 2);
        }
        $bill = [
            'bill_type' => BillTypeEnum::BILL_TYPE_S,
            'bill_status' => BillStatusEnum::PENDING,
            'channel_id' => $form->sale_channel_id,
            'order_sn' => $form->order_sn,
            'order_type' => OrderTypeEnum::ORDER_K,
            'goods_num' => count($bill_goods),
            'total_cost' => $total_cost,
            'total_market' => $total_market,
            'total_sale' => $total_sale,
            'auditor_id' => \Yii::$app->user->identity->getId(),
            'audit_status' => AuditStatusEnum::PENDING,
            'audit_time' => time(),
            'creator_id' => \Yii::$app->user->identity->getId(),
            'created_at' => time(),
        ];

        //1.创建销售单
        \Yii::$app->warehouseService->billS->createBillS($bill, $bill_goods);

        //2.更新商品库存状态
        $condition = ['goods_id'=>$form->goods_ids, 'goods_status' => GoodsStatusEnum::IN_STOCK];
        $execute_num = WarehouseGoods::updateAll(['goods_status'=> GoodsStatusEnum::IN_SALE], $condition);
        if($execute_num <> count($bill_goods)){
            //throw new Exception("货品改变状态数量与明细数量不一致");
        }
        //3.更新订单配货状态
        $form->distribute_status = DistributeStatusEnum::HAS_PEIHUO;
        if(false === $form->save()){
            throw new \Exception($this->getError($form));
        }
        //4.创建订单日志
        return $bill;
    }
}