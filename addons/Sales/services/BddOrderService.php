<?php

namespace addons\Sales\services;


use Yii;
use common\components\Service;
use addons\Shop\common\models\Order;
use addons\Shop\common\enums\OrderStatusEnum;
use addons\Shop\common\models\OrderGoods;
use addons\Shop\common\enums\AttrIdEnum;
use addons\Shop\common\enums\OrderFromEnum;
use addons\Shop\common\models\OrderSync;
use addons\Shop\common\enums\SyncPlatformEnum;
use common\helpers\ArrayHelper;
use addons\Shop\common\enums\PayEnum;

/**
 * Bdd 订单同步
 * Class OrderBddService
 * @package services\common
 */
class BddOrderService extends Service
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
            AttrIdEnum::MATERIAL, //= 10;//材质（成色）
            AttrIdEnum::XIANGKOU, //= 49;//镶口
            AttrIdEnum::CHAIN_TYPE, //= 43;//链类型
            AttrIdEnum::CHAIN_BUCKLE, //= 42;//链扣环
            AttrIdEnum::MAIN_STONE_TYPE,//=56;主石类型
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
        $this->syncAttrIds = ArrayHelper::merge($this->selectAttrIds , $this->inputAttrIds);
    }
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
        $orderInfo = $this->getErpOrderData($order);
        $goodsList = $this->getErpOrderGoodsData($order);
        $addressInfo = $this->getErpOrderAddressData($order);
        $accountInfo = $this->getErpOrderAccountData($order);
        $customerInfo = $this->getErpCustomerData($order);
        try{
            $trans = Yii::$app->trans->beginTransaction();
            $erpOrder = Yii::$app->salesService->order->syncOrder($orderInfo, $accountInfo, $goodsList, $customerInfo, $addressInfo);
            OrderSync::updateAll(['sync_created'=>1,'sync_created_time'=>time()],['order_id'=>$order_id,'sync_platform'=>SyncPlatformEnum::SYNC_EPR]);
            $trans->commit();
        }catch (\Exception $e){
            $trans->rollback();
            throw $e;
        }
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
            "pay_type"=>$this->getErpPayType($order),
            "pay_status"=>$order->payment_status,
            "pay_time"=>$order->payment_time,
            "out_pay_sn"=>$order->pay_sn,
            "out_pay_time"=>$order->payment_time,
            'goods_num'=>array_sum(array_column($order->goods??[],'goods_num')),
            "order_status"=>$this->getErpOrderStatus($order),
            "refund_status"=>0,
            "express_id"=>$this->getErpExpressId($order),
            "express_no"=>$order->express_no,
            "distribute_status"=>$this->getErpDistributeStatus($order),
            "delivery_status"=>$this->getErpDeliveryStatus($order),
            "delivery_time"=>$order->delivery_time,
            "receive_type"=>$order->receive_type,
            "sale_channel_id"=>$this->getErpSaleChannelId($order),
            "order_from"=>$this->getErpOrderFrom($order),
            "order_type"=>$this->getErpOrderType($order),
            "is_invoice"=>$order->is_invoice,
            "out_trade_no"=>$order->order_sn,
            "area_id"=>$order->ip_area_id,
            "customer_name"=>$order->address->realname,
            "customer_mobile"=>$this->getErpCustomerMobile($order),
            "customer_email"=>$order->address->email,
            "customer_message"=>$order->buyer_remark,
            "store_remark"=>$order->seller_remark,
            'order_time'=>$order->created_at,
        ];
    }
    /**
     * ERP 客户资料 表单
     * @param Order $order
     */
    public function getErpCustomerData($order)
    {
        return [
            "firstname"=>$order->address->firstname,
            "lastname"=>$order->address->lastname,
            "realname"=>$order->address->realname,
            "channel_id"=>$this->getErpSaleChannelId($order),
            "source_id"=>1,//BDD官网
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
            "country_id"=>$order->address->country_id/1,
            "province_id"=>$order->address->province_id/1,
            "city_id"=>$order->address->city_id/1,
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
                "style_sn"=> trim($model->style->style_sn ?? '','-1'),
                "goods_sn"=> $model->goods_sn,
                "jintuo_type"=> $this->getErpJintuoType($model),
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
        //echo "<pre/>";
        //print_r($goods_attr);
        // print_r($this->syncAttrIds);
        foreach ($goods_attr as $attr_id=>$val_id){
            if(!in_array($attr_id,$this->syncAttrIds) || $val_id==='') {
                continue;
            }
            ///echo $val_id,'--';
            if($attr_id == AttrIdEnum::MATERIAL) {
                $material = Yii::$app->shopAttr->valueName($val_id);
                list($material_type,$material_color) = $this->getErpMaterialAndColor($material);
                $erp_attrs[] = ['attr_id'=>\addons\Style\common\enums\AttrIdEnum::MATERIAL_TYPE,'attr_value_id'=>$material_type,'attr_value'=>$material];
                $erp_attrs[] = ['attr_id'=>\addons\Style\common\enums\AttrIdEnum::MATERIAL_COLOR,'attr_value_id'=>$material_color,'attr_value'=>$material];
                continue;
            }
            
            $erp_attr_id  = Yii::$app->shopAttr->erpAttrId($attr_id);
            if(!$erp_attr_id) {
                $attr_name = $attr_name ?? Yii::$app->shopAttr->erpAttrId($attr_id);
                throw new \Exception("[ID={$attr_id}]属性未绑定ERP属性ID");
            }
            if(in_array($attr_id,$this->inputAttrIds)) {
                $erp_value_id = 0;
                $erp_value = $val_id;
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
        //print_r($erp_attrs);exit;
        return $erp_attrs;
    }
    /**
     *
     * @param sting $material
     * /**
     BDD官网主成色：
     28     18K白金      【erp材质： 18K     28     金料颜色：246
     29  18K黄金         【erp材质： 18K     28    金料颜色：247
     30   18K玫瑰金     【erp材质： 18K     28       金料颜色：248
     31   14K白金      【erp材质： 14K     33     金料颜色：246
     32   14k黄金          【erp材质： 14K     33    金料颜色：247
     33   14K玫瑰金   【erp材质： 14K     33    金料颜色：248
     34   铂金         【erp材质： Pt950    34    金料颜色：246
     35    银925    【erp材质： Ag925    35    金料颜色：246
     204   合金   【erp材质： Ag925   29
     212   足金    【erp材质： Au999           31   金料颜色：247
     * @return string[]|number[]
     */
    public function getErpMaterialAndColor($material)
    {
        
        $material_type = '';
        $material_type = '';
        if($material == "18K白金") {
            $material_type = 18;
            $material_color = 246;
        }elseif($material == "18K黄金") {
            $material_type = 18;
            $material_color = 247;
        }elseif($material == "18K白金") {
            $material_type = 18;
            $material_color = 246;
        }elseif($material == "18K玫瑰金") {
            $material_type = 18;
            $material_color = 248;
        }elseif($material == "14k黄金") {
            $material_type = 33;
            $material_color = 247;
        }elseif($material == "14K玫瑰金") {
            $material_type = 18;
            $material_color = 248;
        }elseif($material == "铂金") {
            $material_type = 34;
            $material_color = 246;
        }elseif($material == "银925") {
            $material_type = 35;
            $material_color = 246;
        }elseif($material == "合金") {
            $material_type = 35;
            $material_color = 246;
        }elseif($material == "足金") {
            $material_type = 31;
            $material_color = 247;
        }
        return [$material_type,$material_color];
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
     * ERP 订单客户手机
     * @param Order $order
     */
    public static function getErpCustomerMobile($order)
    {
        return trim($order->address->mobile_code,'+').'-'.$order->address->mobile;
    }
    /**
     * ERP 订单销售渠道
     * @param Order $order
     */
    public static function getErpSaleChannelId($order)
    {
        $map = [
            OrderFromEnum::WEB_HK => 1,
            OrderFromEnum::MOBILE_HK => 1,
            OrderFromEnum::WEB_CN => 1,
            OrderFromEnum::MOBILE_CN => 1,
            OrderFromEnum::WEB_US => 2,
            OrderFromEnum::MOBILE_US => 2,
            OrderFromEnum::WEB_TW => 1,
            OrderFromEnum::MOBILE_TW => 1,
        ];
        return $map[$order->order_from]??'';
    }
    /**
     * ERP 订单来源
     * @param Order $order
     */
    public static function getErpOrderFrom($order)
    {
        return \addons\Sales\common\enums\OrderFromEnum::FROM_BDD;
    }
    /**
     * ERP 订单支付方式
     * @param Order $order
     */
    public static function getErpPayType($order)
    {
        $map = [
            PayEnum::PAY_TYPE_PAYPAL => 6,//Paypal
            PayEnum::PAY_TYPE_PAYPAL_1 => 6,//Paypal
            PayEnum::PAY_TYPE_PAYDOLLAR => 3,//PAYDOLLAR
            PayEnum::PAY_TYPE_PAYDOLLAR_1 => 3,//PAYDOLLAR
            PayEnum::PAY_TYPE_PAYDOLLAR_2 => 3,//PAYDOLLAR
            PayEnum::PAY_TYPE_PAYDOLLAR_3 => 3,//PAYDOLLAR
            PayEnum::PAY_TYPE_PAYDOLLAR_4 => 3,//PAYDOLLAR
            PayEnum::PAY_TYPE_CARD => 14,//购物卡
            PayEnum::PAY_TYPE_WIRE_TRANSFER => 13,//电汇
        ];
        if(!isset($map[$order->payment_type])) {
            throw new \Exception("[{$order->order_sn}，{$order->payment_type}]订单支付方式映射失败");
        }
        return $map[$order->payment_type];
    }
    /**
     * ERP 订单配货状态
     * @param Order $order
     */
    public static function getErpDistributeStatus($order)
    {
        $erp_distribute_status = \addons\Sales\common\enums\DistributeStatusEnum::SAVE;
        if($order->order_status >= OrderStatusEnum::ORDER_SEND) {
            $erp_distribute_status = \addons\Sales\common\enums\DistributeStatusEnum::HAS_PEIHUO;
        }
        return $erp_distribute_status;
    }
    /**
     * ERP 订单发货状态
     * @param Order $order
     */
    public static function getErpDeliveryStatus($order)
    {
        $erp_delivery_status = \addons\Sales\common\enums\DeliveryStatusEnum::SAVE;
        if($order->order_status >= OrderStatusEnum::ORDER_SEND) {
            $erp_delivery_status = \addons\Sales\common\enums\DeliveryStatusEnum::HAS_SEND;
        }
        return $erp_delivery_status;
    }
    /**
     * ERP 订单快递方式
     * @param Order $order
     */
    public static function getErpExpressId($order)
    {
        return $order->express_id;
    }
    /**
     * ERP 订单状态
     * @param Order $order
     */
    public static function getErpOrderStatus($order)
    {
        $erp_order_status = \addons\Sales\common\enums\OrderStatusEnum::SAVE;
        if($order->order_status >= OrderStatusEnum::ORDER_SEND) {
            $erp_order_status = \addons\Sales\common\enums\OrderStatusEnum::CONFORMED;
        }
        return $erp_order_status;
    }
    /**
     * ERP 订单类型 1现货 2期货
     * @param Order $order
     */
    public static function getErpOrderType($order)
    {
        return \addons\Sales\common\enums\OrderTypeEnum::FUTURE;
    }
    /**
     * ERP 金托类型
     * @param OrderGoods $goods
     */
    public static function getErpJintuoType($goods)
    {
        $erp_jintuo_type = \addons\Style\common\enums\JintuoTypeEnum::Chengpin;
        if($goods->goods_type == 12) {
            $erp_jintuo_type = \addons\Style\common\enums\JintuoTypeEnum::Kongtuo;
        }
        return $erp_jintuo_type;
    }
}