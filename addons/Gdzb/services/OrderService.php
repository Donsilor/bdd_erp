<?php

namespace addons\Gdzb\services;

use addons\Gdzb\common\models\Customer;
use addons\Gdzb\common\models\Goods;
use addons\Gdzb\common\models\Order;
use addons\Gdzb\common\models\OrderGoods;
use addons\Sales\common\enums\RefundStatusEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use common\enums\ConfirmEnum;
use Yii;
use common\components\Service;
use common\helpers\Url;
use addons\Sales\common\forms\OrderForm;

use addons\Style\common\models\Style;
use common\enums\LogTypeEnum;


/**
 * Class SaleChannelService
 * @package services\common
 */
class OrderService extends Service
{
    /**
     * 顾客订单菜单
     * @param int $order_id
     * @return array
     */
    public function menuTabList($order_id, $returnUrl = null)
    {
        return [
            1=>['name'=>'订单信息','url'=>Url::to(['order/view','id'=>$order_id,'tab'=>1,'returnUrl'=>$returnUrl])],
            2=>['name'=>'日志信息','url'=>Url::to(['order-log/index','order_id'=>$order_id,'tab'=>2,'returnUrl'=>$returnUrl])],
        ];
    }
    /**
     * 人工创建订单
     *
     * @param OrderForm $form
     */
    public function createOrder($form)
    {
        if(false == $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        $isNewOrder = $form->isNewRecord;
        //1.创建订单
        $order = clone $form;
        if(false == $order->save()) {
            throw new \Exception($this->getError($order));
        }

        if($form->isNewRecord){
            $order->order_sn = $this->createOrderSn($order);
        }
        if(false == $order->save()) {
            throw new \Exception($this->getError($order));
        }

        //创建订单日志
        if($isNewOrder === true) {
            $log = [
                'order_id' => $order->id,
                'order_sn' => $order->order_sn,
                'order_status' => $order->order_status,
                'log_type' => LogTypeEnum::ARTIFICIAL,
                'log_time' => time(),
                'log_module' => '创建订单',
                'log_msg' => "创建订单, 订单号:".$order->order_sn
            ];
            \Yii::$app->gdzbService->orderLog->realCreateOrderLog($log);
        }
        return $order;
    }


    /****
     * @param $model
     * @return Customer|array|null|void|\yii\db\ActiveRecord
     * @throws \Exception
     * 同步客户
     */
    public function createSyncCustomer($model){
        $customer_weixin = $model->customer_weixin ?? '';
        if(!$customer_weixin) return;
        $customer = Customer::find()->where(['wechat' => $customer_weixin])->one();
        $isNewRecord = false;
        if(!$customer){
            $isNewRecord = true;
            $consignee_info = json_decode($model->consignee_info,true);
            $customer = new Customer();
            $customer_field = [
                'realname' => $model->customer_name ?? '',
                'mobile' => $model->customer_mobile ?? '',
                'wechat' => $model->customer_weixin ?? '',
                'channel_id' => $model->channel_id ?? 4,
                'source_id' => '',
                'follower_id' => $model->follower_id ?? '',
                'order_num' => 0,
                'order_amount' => 0,
                'country_id' => $consignee_info['country_id'] ?? '',
                'province_id' => $consignee_info['province_id'] ?? '',
                'city_id' => $consignee_info['city_id'] ?? '',
                'address' => $consignee_info['address'] ?? '',
            ];
            $customer->attributes = $customer_field;
        }
        $customer->order_num += 1;
        $customer->order_amount += $model->order_amount;

        if(false === $customer->save()){
            throw new \Exception($this->getError($customer));
        }
        //反写customer_id
        if($isNewRecord == true){
            $model->customer_id = $customer->id;
            if(false === $model->save(true,['customer_id'])){
                throw new \Exception($this->getError($model));
            }
        }
        return $customer;
    }


    /****
     * @param $model
     * @return Customer|array|null|void|\yii\db\ActiveRecord
     * @throws \Exception
     * 同步商品到库存
     */
    public function createSyncGoods($order_id){
        $orderGoods = OrderGoods::find()->where(['order_id' => $order_id])->asArray()->all();
        foreach ($orderGoods as $order_goods){
            $goods = Goods::find()->where(['goods_sn' => $order_goods['goods_sn']])->one();
            if($goods) continue;
            $goods = new Goods();
            $goods->attributes = $order_goods;
            $goods->created_at = time();
            $goods->goods_status = GoodsStatusEnum::IN_SALE;
            if(false === $goods->save()){
                throw new \Exception($this->getError($goods));
            }

        }

        return $orderGoods;
    }





    /**
     * 创建订单编号
     * @param Style $model
     * @throws
     * @return string
     */
    public static function createOrderSn($model,$save = false)
    {
        if(!$model->id) {
            throw new \Exception("创建订单号失败：ID不能为空");
        }
        $order_sn = date("Ymd").str_pad($model->id,8,'0',STR_PAD_LEFT);
        $model->order_sn = $order_sn;
        if($save === true) {
            $result = $model->save(true,['id','order_sn']);
            if($result === false){
                throw new \Exception("保存失败");
            }
        }
        return $model->order_sn;
    }



    /**
     * 订单金额汇总
     * @param unknown $purchase_id
     */
    public function orderSummary($order_id)
    {
        $sum = OrderGoods::find()
            ->select(['sum(1) as total_num','sum(goods_price) as order_amount', 'sum(cost_price) as cost_price',
                'sum(if(is_return =1,1,0)) as refund_num','sum(refund_price) as refund_amount'])
            ->where(['order_id'=>$order_id])
            ->asArray()->one();
        if($sum) {
            $refund_status = $sum['total_num'] > $sum['refund_num'] ? RefundStatusEnum::PART_RETURN : RefundStatusEnum::HAS_RETURN;
            Order::updateAll(['goods_num'=>$sum['total_num'] ?? 0, 'order_amount'=>$sum['order_amount'] ?? 0,
                'cost_price'=>$sum['cost_price'] ?? 0,'refund_num' => $sum['refund_num'] ?? 0,
                'refund_amount'=> $sum['refund_amount'] ?? 0, 'refund_status'=>$refund_status],['id'=>$order_id]);
        }
    }


    public function getOrderAmountNum($where){
        $sum = Order::find()
            ->select(['sum(1) as trade_num','sum(order_amount) as total_order_amount','sum(goods_num) as total_goods_num',
                'sum(refund_amount) as total_refund_amount','sum(refund_num) as total_refund_num'])
            ->where($where)
            ->asArray()->one();
        return $sum;
    }
}