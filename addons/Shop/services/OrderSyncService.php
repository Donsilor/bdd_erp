<?php

namespace addons\Shop\services;


use Yii;
use common\components\Service;
use addons\Shop\common\models\Order;
use addons\Shop\common\enums\OrderStatusEnum;
use addons\Shop\common\models\OrderGoods;

/**
 * Bdd 订单同步
 * Class OrderBddService
 * @package services\common
 */
class OrderSyncService extends Service
{
    /**
     * 同步订单到erp
     * @param int $order_id 订单Id
     */
    public function syncOrder($order_id)
    {   
        //数据校验
        $order = Order::find()->where(['id'=>$order_id])->one();
        if(!$order) {
            throw new \Exception("order查询失败");
        }else if($order->order_status < OrderStatusEnum::ORDER_PAID){
            throw new \Exception("[".OrderStatusEnum::getValue($order->order_status)."]订单状态不被允许同步");
        }        
        if(!$order->account) {
            throw new \Exception("order_account查询失败");
        }
        if(!$order->goods) {
            throw new \Exception("order_goods查询失败");
        }
        if(!$order->address) {
            throw new \Exception("order_address查询失败");
        }
        if(!$order->member) {
            throw new \Exception("member查询失败");
        }
        
        $orderData = $this->getErpOrderData($order);
        $orderGoodsData = $this->getErpOrderGoodsData($order);
        $orderAddressData = $this->getErpOrderAddressData($order);
        $orderAccountData = $this->getErpOrderAccountData($order);
        $customerData = $this->getErpCustomerData($order);
        echo 'finished';
    }
    /**
     * ERP 客户资料 表单
     * @param Order $order
     */
    public function getErpCustomerData($order)
    {
        return [
                "firstname"=>$order->member->firstname,
                "lastname"=>$order->member->lastname,
                "realname"=>$order->member->realname,
                "channel_id"=>$this->getErpSaleChannelId($order),
                "source_id"=>0,
                "head_portrait"=>$order->member->head_portrait,
                "gender"=>$order->member->gender,
                "marriage"=>$order->member->marriage,
                "google_account"=>$order->member->google_account,
                "facebook_account"=>$order->member->facebook_account,
                "qq"=>$order->member->qq,
                "mobile"=>$order->member->mobile,
                "email"=>$order->member->email,
                "birthday"=>$order->member->birthday,
                "home_phone"=>$order->member->home_phone,
                "country_id"=>$order->address->country_id,
                "province_id"=>$order->address->province_id,
                "city_id"=>$order->address->city_id,
                "address"=>$order->address->address_details,
        ];
    }
    /**
     * ERP订单商品 表单
     * @param Order $order
     */
    public function getErpOrderAddressData($order)
    {
        return [
                "country_id"=>$order->address->country_id,
                "province_id"=>$order->address->province_id,
                "city_id"=>$order->address->city_id,
                "firstname"=>$order->address->firstname,
                "lastname"=>$order->address->lastname,
                "realname"=>$order->address->realname,
                "country_name"=>$order->address->country_name,
                "province_name"=>$order->address->province_name,
                "city_name"=>$order->address->city_name,
                "address_details"=>$order->address->address_details,
                "zip_code"=>$order->address->zip_code,
                "mobile"=>$order->address->mobile,
                "mobile_code"=>$order->address->mobile_code,
                "email"=>$order->address->email,
        ];
    }
    /**
     * ERP订单商品 表单
     * @param Order $order
     */
    public function getErpOrderGoodsData($order)
    {
        $erpGoodsList = [];
        foreach ($order->goods ?? [] as $model) {
            $erpGoods = [
                    "goods_name" => $model->goods_name,
                    "goods_image"=> $model->goods_image,
                    "style_sn"=> $model->style->style_sn ?? '',
                    "goods_sn"=> $model->goods_sn,
                    "goods_num"=> $model->goods_num,
                    "goods_price"=> $model->goods_price,
                    "goods_pay_price"=> $model->goods_pay_price,
                    "goods_discount"=> ($model->goods_price - $model->goods_pay_price)/1,
                    "currency"=> $model->currency,
                    "exchange_rate"=> $model->exchange_rate,
                    "delivery_status"=> $this->getErpDeliveryStatus($order),
                    "is_stock"=>0,
                    "is_gift"=>0,
                    "goods_attrs"=>$this->getErpOrderGoodsAttrsData($model),                  
            ];
            $erpGoodsList[] = $erpGoods;
        }
        
        return $erpGoodsList;
    }
    /**
     * ERP订单商品属性表单
     * @param OrderGoods $model 订单商品Model
     */
    public function getErpOrderGoodsAttrsData($model)
    {
        $goods_spec = json_decode($model->goods_spec,true);
        $goods_attr = json_decode($model->goods_attr,true);
        foreach ($goods_spec as $id=>$val){
            //$attr_name = Yii::$app->attr
        }
        print_r($goods_spec);
        print_r($goods_attr);
    }
    /**
     * ERP订单金额表单
     * @param Order $order
     */
    public function getErpOrderAccountData($order)
    { 
        return  [
            "order_amount"=>$order->account->order_amount,
            "goods_amount"=>$order->account->goods_amount,
            "discount_amount"=>$order->account->discount_amount,
            "pay_amount"=>$order->account->pay_amount,
            "refund_amount"=>$order->account->refund_amount,
            "shipping_fee"=>$order->account->shipping_fee,
            "tax_fee"=>$order->account->tax_fee,
            "safe_fee"=>$order->account->safe_fee,
            "other_fee"=>$order->account->other_fee,
            "exchange_rate"=>$order->account->exchange_rate,
            "currency"=>$order->account->currency,
            "coupon_amount"=>$order->account->coupon_amount,
            "card_amount"=>$order->account->card_amount,
            "paid_amount"=>$order->account->paid_amount,
            "paid_currency"=>$order->account->paid_currency,
        ];
    }
    /**
     * ERP订单主表表单
     * @param Order $order
     */
    public function getErpOrderData($order)
    {
        return [
            "language"=>$order->language,
            "currency"=>$order->account->currency,
            "pay_sn"=>$order->pay_sn,
            "pay_type"=>$this->getErpPayType($order),
            "pay_status"=>$order->payment_status,
            "pay_time"=>$order->payment_time,
            "order_status"=>$order->payment_time, 
            "refund_status"=>0,
            "express_id"=>0,
            "express_no"=>0,
            "delivery_status"=>0,
            "delivery_time"=>$order->delivery_time,
            "receive_type"=>0,
            "sale_channel_id"=>$this->getErpSaleChannelId($order),
            "order_from"=>$this->getErpOrderFrom($order),
            "is_invoice"=>$order->is_invoice,
            "out_trade_no"=>$order->order_sn,
            "area_id"=>$order->ip_area_id,
            "customer_name"=>$order->address->realname,
            "customer_mobile"=>$order->address->mobile,
            "customer_email"=>$order->address->email,
            "customer_message"=>$order->buyer_remark,
            "store_remark"=>$order->seller_remark,
        ];
    }
    /**
     * ERP销售渠道
     * @param Order $order
     */
    public static function getErpSaleChannelId($order)
    {
         return 1;
    }
    /**
     * ERP 订单来源
     * @param Order $order
     */
    public static function getErpOrderFrom($order)
    {
        return 0;
    }
    /**
     * ERP 订单支付方式
     * @param Order $order
     */
    public static function getErpPayType($order)
    {
        return 0;
    }
    /**
     * ERP 订单发货状态
     * @param Order $order
     */
    public function getErpDeliveryStatus($order)
    {
        return 0;
    }
}