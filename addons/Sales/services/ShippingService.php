<?php

namespace addons\Sales\services;

use Yii;
use common\components\Service;
use addons\Sales\common\models\Order;
use addons\Sales\common\forms\ShippingForm;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Sales\common\enums\DeliveryStatusEnum;
use addons\Sales\common\enums\DistributeStatusEnum;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Style\common\enums\LogTypeEnum;
use addons\Sales\common\enums\RefundStatusEnum;
use common\helpers\Url;
use common\helpers\ArrayHelper;
use yii\db\Exception;

/**
 * Class ShippingService
 * @package services\common
 */
class ShippingService extends Service
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
            1=>['name'=>'质检详情','url'=>Url::to(['order/order-fqc','id'=>$order_id,'tab'=>1,'returnUrl'=>$returnUrl])],
            //2=>['name'=>'日志信息','url'=>Url::to(['order-log/index','order_id'=>$order_id,'tab'=>2,'returnUrl'=>$returnUrl])],
        ];
    }

    /**
     * 订单发货
     * @param ShippingForm $form
     * @return object
     * @throws
     */
    public function orderShipping($form)
    {
        $order = Order::find()->where(['order_sn' => $form->order_sn])->one();
        if(!$order){
            throw new \Exception('订单号：'.$form->order_sn.'未查到订单信息');
        }
        if($order->distribute_status != DistributeStatusEnum::HAS_PEIHUO){
            throw new \Exception('订单号：'.$form->order_sn.'不是已配货状态不能发货');
        }
        if($order->delivery_status != DeliveryStatusEnum::TO_SEND){
            throw new \Exception('订单号：'.$form->order_sn.'不是待发货状态不能发货');
        }
        if($order->refund_status != RefundStatusEnum::SAVE){
            throw new \Exception('订单号：'.$form->order_sn.'已退款或退款申请中，不能发货');
        }
        //销售单审核
        $bill = WarehouseBill::find()->where(['order_sn'=>$form->order_sn])->one();
        if(!$bill){
            throw new \Exception('订单号：'.$form->order_sn.'未查到S销售单');
        }
        $bill->bill_status = BillStatusEnum::CONFIRM;
        if(false === $bill->save()){
            throw new \Exception($this->getError($bill));
        }
        $billG = WarehouseBillGoods::find()->select(['goods_id'])->where(['bill_id'=>$bill->id])->all();
        if(!$billG){
            throw new \Exception('订单号：'.$form->order_sn.'对应销售单明细不能为空');
        }
        $goods_ids = ArrayHelper::getColumn($billG,'goods_id');
        //更新库存信息
        $execute_num = WarehouseGoods::updateAll(['goods_status'=>GoodsStatusEnum::HAS_SOLD], ['goods_id'=>$goods_ids, 'goods_status'=>GoodsStatusEnum::IN_SALE]);
        if($execute_num <> count($goods_ids)){
            throw new \Exception("货品改变状态数量与明细数量不一致");
        }

        //销售单 - 发货-- 插入商品日志
        foreach ($goods_ids as $goods_id){
            $log = [
                'goods_id' => $goods_id,
                'goods_status' => GoodsStatusEnum::HAS_SOLD,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_msg' => '销售单：'.$bill->bill_no.";货品状态:“".GoodsStatusEnum::getValue(GoodsStatusEnum::IN_STOCK)."”变更为：“".GoodsStatusEnum::getValue(GoodsStatusEnum::HAS_SOLD)."”"
            ];
            Yii::$app->warehouseService->goodsLog->createGoodsLog($log);
        }




        //更新订单信息
        $order->delivery_status = DeliveryStatusEnum::HAS_SEND;
        $order->delivery_time = time();
        $order->express_id = $form->express_id;
        $order->express_no = $form->freight_no;
        if(false === $order->save()){
            throw new \Exception($this->getError($order));
        }
        
        //创建快递单
        $form->sale_channel_id = $order->sale_channel_id;
        $form->out_trade_no = $order->out_trade_no;
        $form->creator_id = \Yii::$app->user->identity->getId();
        $form->created_at = time();
        if(false === $form->save()){
            throw new \Exception($this->getError($form));
        }
        
        //创建订单日志
        $log = [
                'order_id' => $order->id,
                'order_sn' => $order->order_sn,
                'order_status' => $order->order_status,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_time' => time(),
                'log_module' => '订单发货',
                'log_msg' => "订单发货, 快递单号:".$order->express_no
        ];
        \Yii::$app->salesService->orderLog->createOrderLog($log);
        return $order;
    }
}