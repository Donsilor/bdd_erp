<?php

namespace addons\Sales\services;


use Yii;
use common\components\Service;
use addons\Sales\common\enums\JdAttrEnum;

/**
 * Bdd 订单同步
 * Class OrderBddService
 * @package services\common
 */
class JdOrderService extends Service
{
    /**
     * 同步订单到erp
     * @param int $order_id 订单Id
     */
    public function syncOrder($order)
    {
        if(!$order) {
            throw new \Exception("order不能为空");
        }
        if(!$order->itemInfoList) {
            throw new \Exception("order->itemInfoList不能为空");
        }
        if(!$order->consigneeInfo) {
            throw new \Exception("order->consigneeInfo不能为空");
        }
        $orderInfo = $this->getErpOrderData($order);
        $goodsList = $this->getErpOrderGoodsData($order);
        $addressInfo = $this->getErpOrderAddressData($order);
        $accountInfo = $this->getErpOrderAccountData($order);
        $customerInfo = $this->getErpCustomerData($order);
        $invoiceInfo = $this->getErpOrderInvoiceData($order);
        //print_r(['invoiceInfo'=>$invoiceInfo,'goodsList'=>$goodsList,'addressInfo'=>$addressInfo,'accountInfo'=>$accountInfo,'customerInfo'=>$customerInfo]);
        //exit;
        try{
            $trans = Yii::$app->trans->beginTransaction();
            Yii::$app->salesService->order->syncOrder($orderInfo, $accountInfo, $goodsList, $customerInfo, $addressInfo, $invoiceInfo);            
            $trans->commit();
        }catch (\Exception $e){
            $trans->rollback();
            throw $e;
        }
    }
    /**
     * 同步订单到erp
     * @param int $order_id 订单Id
     */
    public function syncOrderGoods($ware,$order_ids = [])
    {
        if(!$ware) {
            throw new \Exception("ware不能为空");
        }
        if(!$ware->multiCateProps) {
            throw new \Exception("ware->multiCateProps不能为空");
        }
        $goods_attrs = [];
        $goods_specs = [];
        foreach ($ware->multiCateProps as $prop) {
             $attr_list = \Yii::$app->jdSdk->getAttrList($ware->multiCategoryId);
             $attrName = JdAttrEnum::getAttrName($prop->attrId,$attr_list); 
             if($attrName && count($prop->attrValueAlias)==1) {
                 $goods_specs[$attrName] = implode(',',$prop->attrValueAlias);
             }
             /* $attr_id = JdAttrEnum::getAttrId($prop->attrId);
             if($attr_id) {
                 $attr_value_id = JdAttrEnum::getValueId($prop->attrId,$prop->attrValueAlias[0]);
                 if($attr_value_id) {
                     $goods_attrs[] = ['attr_id'=>$attr_id,'attr_value_id'=>$attr_value_id,'attr_value'=>''];
                 }
             } */
             
        }     
        if(!empty($goods_specs)) {
            Yii::$app->salesService->order->syncOrderGoodsSpec($ware->wareId,$goods_specs);
        }
        if(!empty($goods_attrs)) {
            Yii::$app->salesService->order->syncOrderGoodsAttr($ware->wareId,$goods_attrs,$order_ids);
        }
    }    
    /**
     * ERP订单主表表单
     * @param Order $order
     */
    public function getErpOrderData($order)
    {
        //$store_remark = ($order->venderRemark ?? '').';京东买家账户:'.($order->open_id_buyer ?? '');
        return [
            "language"=>'zh-CN',
            "currency"=>'CNY',
            "pay_type"=>$this->getErpPayType($order),//京东（平台支付）
            "pay_status"=>$this->getErpPayStatus($order),//已支付
            "pay_time"=> !empty($order->paymentConfirmTime) ? strtotime($order->paymentConfirmTime):null,
            //"out_pay_sn"=>$order->order_id,
            //"out_pay_time"=>$order->payment_time,
            'goods_num'=>1,
            "order_status"=>$this->getErpOrderStatus($order),
            "refund_status"=>0,
            "express_id"=>$this->getErpExpressId($order),
            "express_no"=>$order->waybill,
            "distribute_status"=>$this->getErpDistributeStatus($order),
            "delivery_status"=>$this->getErpDeliveryStatus($order),
            "delivery_time"=>null,
            "receive_type"=>$this->getErpReceiveType($order),//送货类型
            "sale_channel_id"=>$this->getErpSaleChannelId($order),
            "order_from"=>$this->getErpOrderFrom($order),
            "order_type"=>$this->getErpOrderType($order),
            "is_invoice"=>0,
            "out_trade_no"=>$order->orderId,
            //"area_id"=>,
            "customer_name"=>$order->consigneeInfo->fullname,
            "customer_mobile"=>$this->getErpCustomerMobile($order),
            //"customer_email"=>$order->consigneeInfo->email,
            "customer_message"=>$order->orderRemark,
            "store_remark"=>($order->venderRemark ?? ''),
            'order_time'=>strtotime($order->orderStartTime),
        ];
    }
    /**
     * ERP订单金额表单
     * @param Order $order
     */
    public function getErpOrderAccountData($order)
    {
        return  [
                "order_amount"=>$order->orderTotalPrice,
                "goods_amount"=>$this->getErpGoodsAmount($order),
                "discount_amount"=>$order->sellerDiscount,
                "pay_amount"=>$order->orderPayment,
                "refund_amount"=>0,
                "shipping_fee"=>$order->freightPrice,
                "tax_fee"=>0,
                "safe_fee"=>0,
                "other_fee"=>0,
                "exchange_rate"=>1,
                "currency"=>'CNY',
                "coupon_amount"=>0,
                "card_amount"=>0,
                "paid_amount"=>$order->orderPayment,
                "paid_currency"=>'CNY',
        ];
    }
    /**
     * ERP 客户资料 表单
     * @param Order $order
     */
    public function getErpCustomerData($order)
    {
        return [
            //"firstname"=>$order->address->firstname,
            //"lastname"=>$order->address->lastname,
            "realname"=>$order->consigneeInfo->fullname,
            "channel_id"=>$this->getErpSaleChannelId($order),
            "source_id"=>5,//京东商城
            //"head_portrait"=>$order->member->head_portrait,
            //"gender"=>$order->member->gender,
            //"marriage"=>$order->member->marriage,
            //"google_account"=>$order->member->google_account,
            //"facebook_account"=>$order->member->facebook_account,
            //"qq"=>$order->member->qq,
            "mobile"=>$this->getErpCustomerMobile($order),
            "email"=>$order->invoiceEasyInfo->invoiceConsigneeEmail ?? '',
            //"birthday"=>$order->member->birthday,
            "home_phone"=>$order->consigneeInfo->telephone,
            //"country_id"=>$order->address->country_id,
            //"province_id"=>$order->address->province_id,
            //"city_id"=>$order->address->city_id,
            "address"=>$order->consigneeInfo->fullAddress,
        ];
    }
    /**
     * ERP订单商品 表单
     * @param Order $order
     */
    public function getErpOrderAddressData($order)
    {
        return [
            "country_id"=>0,
            "province_id"=>0,
            "city_id"=>0,
            //"firstname"=>$order->address->firstname,
            //"lastname"=>$order->address->lastname,
            "realname"=>$order->consigneeInfo->fullname,
            "country_name"=>'中国',
            "province_name"=>$order->consigneeInfo->province,
            "city_name"=>$order->consigneeInfo->city,
            "address_details"=>$order->consigneeInfo->fullAddress,
            //"zip_code"=>$order->address->zip_code,
            "mobile"=>$order->consigneeInfo->mobile,
            "mobile_code"=>'+86',
            //"email"=>$order->address->email,
        ];
    }
    /**
     * ERP订单发票  表单
     * @param Order $order
     */
    public function getErpOrderInvoiceData($order)
    {   
        
        $title_type = null;
        $invoice_type = null;
        $is_invoice = 0;
        $invoice_title = null;
        if($order->invoiceEasyInfo->invoiceType > 0) {
            if($order->invoiceEasyInfo->invoiceTitle != '个人') {
                $title_type = \addons\Sales\common\enums\InvoiceTitleTypeEnum::ENTERPRISE;
            }else{
                $title_type = \addons\Sales\common\enums\InvoiceTitleTypeEnum::PERSONAL;
            } 
            $invoice_type = $order->invoiceEasyInfo->invoiceType;
            $is_invoice = 1;
            $invoice_title = $order->invoiceEasyInfo->invoiceTitle ?? '';
        }              
        return [
                'is_invoice' =>$is_invoice,
                'title_type' =>$title_type,
                'invoice_type'=>$invoice_type,
                'invoice_title'=>$invoice_title,
                'tax_number'=>$order->invoiceEasyInfo->invoiceCode ?? '',
                'email'=> $order->invoiceEasyInfo->invoiceConsigneeEmail ?? '',
                'mobile'=> $order->invoiceEasyInfo->invoiceConsigneePhone ?? ''              
        ];
    }
    /**
     * ERP订单商品 表单
     * @param Order $order
     */
    public function getErpOrderGoodsData($order)
    {
        $erpGoodsList = [];
        foreach ($order->itemInfoList ?? [] as $model) {
            if(!$model->productNo) {
                continue;
            }
            for($i =1; $i<= $model->itemTotal; $i++) {
                $goods_discount = 0;
                foreach ($order->couponDetailList ?? [] as $coupon) {
                    if(($coupon->skuId ?? '') == $model->skuId) {
                        $goods_discount += ($coupon->couponPrice/$model->itemTotal);
                    }
                }
                if(!$model->skuId || !$model->wareId) {
                    throw new \Exception($model->productNo." skuId or wareId is empty");
                }
                $erpGoods = [
                    "goods_name" => $model->skuName,
                    "goods_image"=> null,
                    "style_sn"=> $model->productNo,
                    "out_sku_id"=> $model->skuId,
                    "out_ware_id"=> $model->wareId,
                    "jintuo_type"=> $this->getErpJintuoType($model),
                    "goods_num"=> 1,
                    "goods_price"=> $model->jdPrice,
                    "goods_pay_price"=> $model->jdPrice - $goods_discount,
                    "goods_discount"=> $goods_discount,
                    "currency"=> 'CNY',
                    "exchange_rate"=> 1,
                    "delivery_status"=> $this->getErpDeliveryStatus($order),
                    "is_stock"=>0,
                    "is_gift"=>$model->productNo ? 0:1,
                    //"goods_attrs"=>$this->getErpOrderGoodsAttrsData($model),
                ];
                $erpGoodsList[] = $erpGoods;
            }
        }
        
        return $erpGoodsList;
    }
    
    /**
     * ERP订单商品属性表单
     * <ul class="pop-select-dropdown-list"><li class="pop-select-item" style="display: none;">FL/无暇</li>
     * <li class="pop-select-item" style="display: none;">IF/镜下无暇</li>
     * <li class="pop-select-item" style="display: none;">VVS/极微瑕</li>
     * <li class="pop-select-item" style="display: none;">VS/微瑕</li>
     * <li class="pop-select-item pop-select-item-selected">SI/小瑕</li>
     * <li class="pop-select-item" style="display: none;">P/不洁净</li><li class="pop-select-item" style="display: none;">不分级</li></ul>
     * @param OrderGoods $model 订单商品Model
     */
    public function getErpOrderGoodsAttrsData($ware)
    {       
         return [];
    }
    
    /**
     * 收货（时间）类型
     * @param unknown $order
     * @return number
     */
    public function getErpReceiveType($order) 
    {
        $receiveType = 1;
        if($order->deliveryType == '任意时间') {
            $receiveType = 1;
        }
        return $receiveType;
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
     * 获取商品总金额
     * @param unknown $order
     */
    public static function getErpGoodsAmount($order)
    {
        $goods_amount = 0;
        foreach ($order->itemInfoList as $goods){
            $goods_amount += $goods->jdPrice * $goods->itemTotal;
        }
        return $goods_amount;
    }
    /**
     * ERP 订单支付状态
     * @param Order $order
     */
    public static function getErpPayStatus($order)
    {
        return 1;//已支付
    }
    /**
     * ERP 订单客户手机
     * @param Order $order
     */
    public static function getErpCustomerMobile($order)
    {
        return $order->consigneeInfo->mobile;
    }
    /**
     * ERP 订单销售渠道
     * @param Order $order
     */
    public static function getErpSaleChannelId($order)
    {
        return 6;//京东渠道
    }
    /**
     * ERP 订单来源
     * @param Order $order
     */
    public static function getErpOrderFrom($order)
    {
        return \addons\Sales\common\enums\OrderFromEnum::FROM_JD;
    }
    /**
     * ERP 订单支付方式
     * @param Order $order
     */
    public static function getErpPayType($order)
    {
         return 8; //京东平台支付
    }
    /**
     * ERP 订单配货状态
     * @param Order $order
     */
    public static function getErpDistributeStatus($order)
    {
        $erp_distribute_status = \addons\Sales\common\enums\DistributeStatusEnum::SAVE;
        $map = [
            'WAIT_GOODS_RECEIVE_CONFIRM '=>\addons\Sales\common\enums\DistributeStatusEnum::HAS_PEIHUO,
            'FINISHED_L' =>\addons\Sales\common\enums\DistributeStatusEnum::HAS_PEIHUO
        ];
        return $map[$order->orderState] ?? $erp_distribute_status;
    }
    /**
     * ERP 订单发货状态
     * @param Order $order
     */
    public static function getErpDeliveryStatus($order)
    {
        $erp_delivery_status = \addons\Sales\common\enums\DeliveryStatusEnum::SAVE;
        $map = [
            'WAIT_GOODS_RECEIVE_CONFIRM '=>\addons\Sales\common\enums\DeliveryStatusEnum::HAS_SEND,
            'FINISHED_L' =>\addons\Sales\common\enums\DeliveryStatusEnum::HAS_SEND
        ];
        return $map[$order->orderState] ?? $erp_delivery_status;
    }
    /**
     * ERP 订单快递方式
     * @param Order $order
     */
    public static function getErpExpressId($order)
    {
        $erp_express_id = 5;//京东快递
        $map = [
            2087=>5,//京东快递
            1499=>6,//中通快递
            467=>2//顺丰快递
        ];
        return $map[$order->logisticsId] ?? $erp_express_id;
    }
    /**
     * ERP 订单状态
     * @param Order $order
     */
    public static function getErpOrderStatus($order)
    {
        $erp_order_status = \addons\Sales\common\enums\OrderStatusEnum::SAVE;
        $map = [
            'WAIT_SELLER_STOCK_OUT'=>\addons\Sales\common\enums\OrderStatusEnum::SAVE,
            'WAIT_GOODS_RECEIVE_CONFIRM '=>\addons\Sales\common\enums\OrderStatusEnum::CONFORMED,
            'FINISHED_L' =>\addons\Sales\common\enums\OrderStatusEnum::CONFORMED
        ];
        return $map[$order->orderState] ?? $erp_order_status;
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
        return \addons\Style\common\enums\JintuoTypeEnum::Chengpin;       
    }
}