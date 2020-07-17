<?php

namespace addons\Shop\services;


use Yii;
use common\components\Service;
use addons\Shop\common\models\Order;
use addons\Shop\common\enums\OrderStatusEnum;
use addons\Shop\common\models\OrderGoods;
use addons\Shop\common\enums\AttrIdEnum;

/**
 * Bdd 订单同步
 * Class OrderBddService
 * @package services\common
 */
class OrderSyncService extends Service
{   
    //需要同步的属性ID数组
    public $syncAttrIds;
    //输入类型属性ID数组
    public $inputAttrIds;
    //单选类型属性ID数组
    public $selectAttrIds;
    
    public function init()
    {
         $this->selectAttrIds = [
                 AttrIdEnum::FINGER, //= 38;//美号（手寸）
                // AttrIdEnum::MATERIAL, //= 10;//材质（成色）
                 AttrIdEnum::XIANGKOU, //= 49;//镶口
                 AttrIdEnum::CHAIN_TYPE, //= 43;//链类型
                 AttrIdEnum::CHAIN_BUCKLE, //= 42;//链扣环
                 AttrIdEnum::DIA_CLARITY, //= 2;//钻石净度
                 AttrIdEnum::DIA_CUT, //= 4;//钻石切工
                 AttrIdEnum::DIA_SHAPE, //= 6;//钻石形状
                 AttrIdEnum::DIA_COLOR, //= 7;//钻石颜色
                 AttrIdEnum::DIA_FLUORESCENCE, //= 8;//荧光
                 AttrIdEnum::DIA_CERT_TYPE, //= 48;//证书类型
                 AttrIdEnum::DIA_POLISH, //= 28;//抛光
                 AttrIdEnum::DIA_SYMMETRY, //= 29;//对称
         ];
         
         $this->inputAttrIds = [
                 AttrIdEnum::JINZHONG, //= 11;//金重
                 AttrIdEnum::CHAIN_LENGTH, //= 53;//链长
                 AttrIdEnum::HEIGHT, //= 41;//高度（mm）
                 AttrIdEnum::DIA_CARAT, //= 59;//钻石大小
                 AttrIdEnum::DIA_CERT_NO, //= 31;//证书编号
                 AttrIdEnum::DIA_CUT_DEPTH, //= 32;//切割深度（%）
                 //AttrIdEnum::DIA_TABLE_LV, //= 33;//台宽比（%）
                 //AttrIdEnum::DIA_LENGTH, //= 34;//长度（mm）
                 //AttrIdEnum::DIA_WIDTH, //= 35;//宽度（mm）
                 AttrIdEnum::DIA_ASPECT_RATIO, //= 36;//长宽比（%）
                 AttrIdEnum::DIA_STONE_FLOOR, //= 37;//石底层
         ];
         $this->syncAttrIds = $this->selectAttrIds + $this->inputAttrIds;
    }
    /**
     * 同步订单到erp
     * @param int $order_id 订单Id
     */
    public function syncOrder($order_id)
    {   $order_id = 1407;
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
        echo '<pre/>';
        $orderData = $this->getErpOrderData($order);print_r($orderData);
        $orderGoodsData = $this->getErpOrderGoodsData($order);print_r($orderGoodsData);
        $orderAddressData = $this->getErpOrderAddressData($order);print_r($orderAddressData);
        $orderAccountData = $this->getErpOrderAccountData($order);print_r($orderAccountData);
        $customerData = $this->getErpCustomerData($order);print_r($customerData);
        
        echo 'Finished';
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
                "mobile"=>$this->getErpCustomerMobile($order),
                "email"=>$order->address->email,
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
        $goods_spec = json_decode($model->goods_spec,true) ?? [];
        $goods_attr = json_decode($model->goods_attr,true) ??[];
        $goods_attr = $goods_attr + $goods_spec;
        $erp_attrs = [];
        foreach ($goods_attr as $attr_id=>$val_id){
            if(!in_array($attr_id,$this->syncAttrIds) || $val_id==='') {
                continue;
            }
            $erp_attr_id  = Yii::$app->shopAttr->erpAttrId($attr_id);
            if(!$erp_attr_id) {
                $attr_name = $attr_name ?? Yii::$app->shopAttr->erpAttrId($attr_id);
                throw new \Exception("[ID={$attr_id}]属性未绑定ERP属性ID");
            }
            if(in_array($attr_id,$this->inputAttrIds)) {
                 $erp_value_id = 0;
                 $erp_value = 0;
            }elseif(in_array($attr_id,$this->selectAttrIds)){                
                $erp_value_id  = Yii::$app->shopAttr->erpValueId($val_id);
                if(!$erp_value_id) {
                    throw new \Exception("[ID={$attr_id},{$val_id}] 属性值未绑定ERP属性值ID");
                }
                $erp_value = Yii::$app->shopAttr->valueName($val_id);
            }else {
                continue;
            }
            $erp_attrs[] = ['attr_id'=>$erp_attr_id,'attr_value_id'=>$erp_value_id,'attr_value'=>$erp_value];
        }
        print_r($erp_attrs);
        return $erp_attrs;
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
            "customer_mobile"=>$this->getErpCustomerMobile($order),
            "customer_email"=>$order->address->email,
            "customer_message"=>$order->buyer_remark,
            "store_remark"=>$order->seller_remark,
        ];
    }
    public static function getErpCustomerMobile($order)
    {
        return trim($order->address->mobile_code,'+').'-'.$order->address->mobile;
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