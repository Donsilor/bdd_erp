<?php

namespace addons\Sales\services;

use addons\Sales\common\models\OrderGoods;
use addons\Sales\common\models\OrderGoodsAttribute;
use Yii;
use common\components\Service;
use common\helpers\Url;
use addons\Sales\common\forms\OrderForm;
use addons\Sales\common\models\OrderAccount;
use addons\Sales\common\models\Customer;
use addons\Sales\common\models\Order;
use addons\Sales\common\models\OrderAddress;
use addons\Supply\common\enums\BuChanEnum;
use common\enums\AuditStatusEnum;
use addons\Supply\common\enums\FromTypeEnum;
use addons\Sales\common\enums\IsStockEnum;
use addons\Style\common\models\Style;

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
        //1.创建订单
        $order = clone $form;
        $order->creator_id  = \Yii::$app->user->identity->id;
        if(false == $order->save()) {
            throw new \Exception($this->getError($order));
        }
        $customer = Customer::find()->where(['mobile'=>$order->customer_mobile,'channel_id'=>$order->sale_channel_id])->one();
        if(!$customer) {          
            //2.创建用户信息
            $customer = new Customer();
            $customer->realname = $order->customer_name;
            $customer->mobile = $order->customer_mobile;
            $customer->email = $order->customer_email;
            $customer->channel_id = $order->sale_channel_id;
            if(false == $customer->save()) {
                throw new \Exception("创建用户失败：".$this->getError($customer));
            }
        }else{
            //更新用户信息
            $customer->realname = $customer->realname ? $customer->realname : $order->customer_name;
            $customer->mobile = $customer->mobile ? $customer->mobile: $order->customer_mobile;
            $customer->email = $customer->email ? $customer->email : $order->customer_email;
            if(false == $customer->save()) {
                throw new \Exception("更新用户失败：".$this->getError($customer));
            }
        }
        $order->customer_id = $customer->id;
        if($form->isNewRecord){
            $order->order_sn = $this->createOrderSn($order);
        }
        if(false == $order->save()) {
            throw new \Exception($this->getError($order));
        }
        //3.创建订单金额
        if($form->isNewRecord){
            $account = new OrderAccount();
            $account->order_id = $order->id;
            if(false == $account->save()) {
                throw new \Exception($this->getError($account));
            }
        }     
        //4.同步订单收货地址
        $address = OrderAddress::find()->where(['order_id'=>$order->id])->one();
        if(!$address) {
            $address = new OrderAddress();
            $address->order_id = $order->id;
        } 
        if($address->customer_id != $customer->id) {            
            $address->customer_id = $customer->id;
            $address->realname = $customer->realname;
            $address->mobile = $customer->mobile;
            $address->email = $customer->email;
            $address->country_id = $customer->country_id;
            $address->province_id = $customer->province_id;
            $address->city_id = $customer->city_id;
            $address->address_details = $customer->address_details;
            //$address->zip_code = $customer->zip_code;
        }        
        if(false == $address->save(false)) {
            throw new \Exception("同步收货地址失败：".$this->getError($address));
        }

        return $order;        
    }
    /**
     * 自动创建同步订单
     * @param unknown $orderInfo
     * @param unknown $accountInfo
     * @param unknown $goodsList
     * @param unknown $customerInfo
     * @param unknown $addressInfo
     * @param array $noticeInfo
     * @throws \Exception
     * @return \addons\Sales\common\models\Order
     */
    public function syncOrder($orderInfo, $accountInfo, $goodsList, $customerInfo, $addressInfo, $noticeInfo = [])
    {
        if(empty($orderInfo['out_trade_no'])) {
            throw new \Exception("orderInfo->out_trade_no 不能为空");
        }
        //1.同步订单
        $is_new = false;
        $order = Order::find()->where(['out_trade_no'=>$orderInfo['out_trade_no']])->one();
        if(!$order) {
            $is_new = true;
            $order = new Order();
        }
        $order->attributes = $orderInfo;
        if(false === $order->save()) {
             throw new \Exception($this->getError($order));
        }        
        //2.同步订单金额
        $account = OrderAccount::find()->where(['order_id'=>$order->id])->one();
        if(!$account) {
            $account = new OrderAccount();
            $account->order_id = $order->id;
        }
        $account->attributes = $accountInfo;
        if(false == $account->save()) {
            throw new \Exception($this->getError($account));
        }
        //3.同步订单商品明细
        if($is_new === false) {
            foreach ($goodsList as $goodsInfo) {
                $style_sn = $goodsInfo['style_sn'] ?? '';
                $style = Style::find()->where(['style_sn'=>$style_sn])->one();
                if(!$style) {
                    throw new \Exception("同步订单商品失败：[{$style_sn}]款号在erp系统不存在");
                }
                //从款式信息自动带出款的信息
                $styleInfo = $style->toArray(['style_cate_id','product_type_id','is_inlay','style_channel_id','style_sex']);
                $orderGoods = new OrderGoods();
                $orderGoods->attributes = $goodsInfo + $styleInfo;
                $orderGoods->order_id = $order->id;
                if(false === $orderGoods->save()) {
                    throw new \Exception("同步订单商品失败：".$this->getError($orderGoods));
                }
                /**
                 * [attr_id] => 6[attr_value_id] => 16[attr_value] => 圆形
                 */
                foreach ($goodsInfo['goods_attrs'] ??[] as $attr) {
                    $goodsAttr = new OrderGoodsAttribute();
                    $goodsAttr->attributes = $attr;
                    if($goodsAttr->attr_value_id) {
                        $goodsAttr->attr_value = Yii::$app->attr->valueName($goodsAttr->attr_value_id);
                    }
                    $goodsAttr->id = $orderGoods->id;
                    if(false === $goodsAttr->save()) {
                        throw new \Exception("同步商品属性失败：".$this->getError($goodsAttr));
                    }
                }
            }
        }
        //4.同步客户信息
        $customer = Customer::find()->where(['mobile'=>$order->customer_mobile,'channel_id'=>$order->sale_channel_id])->one();
        if(!$customer) {
            //2.创建用户信息
            $customer = new Customer();
            $customer->attributes = $customerInfo;
            $customer->channel_id = $order->sale_channel_id;
            if(false == $customer->save()) {
                throw new \Exception("创建用户失败：".$this->getError($customer));
            }
        }else{
            //更新用户信息
            $customer->realname = $customer->realname ? $customer->realname : $order->customer_name;
            $customer->mobile = $customer->mobile ? $customer->mobile: $order->customer_mobile;
            $customer->email = $customer->email ? $customer->email : $order->customer_email;
            //$customer->attributes = $customerInfo;
            if(false == $customer->save()) {
                throw new \Exception("更新用户失败：".$this->getError($customer));
            }
        }
        $order->customer_id = $customer->id;
        if($is_new === true){
            $order->order_sn = $this->createOrderSn($order);
        }
        if(false == $order->save()) {
            throw new \Exception($this->getError($order));
        }
        
        //5.同步订单收货地址
        $address = OrderAddress::find()->where(['order_id'=>$order->id])->one();
        if(!$address) {
            $address = new OrderAddress();            
            $address->order_id = $order->id;
        }
        $address->attributes = $addressInfo;     
        if(false == $address->save()) {
            throw new \Exception("同步收货地址失败：".$this->getError($address));
        }  
        //5.同步发票
        return $order;        
    }
    /**
     * 同步订单商品生成布产单
     * @param int $order_id
     * @param array $detail_ids
     * @throws \Exception
     */
    public function syncProduce($order_id, $detail_ids = null)
    {
        $order = Order::find()->where(['id'=>$order_id])->one();
        if($order->total_num <= 0 ){
            throw new \Exception('订单没有明细');
        }
        if($order->audit_status != AuditStatusEnum::PASS){
            throw new \Exception('订单没有审核');
        }
        $query = OrderGoods::find()->where(['order_id'=>$order_id,'is_stock'=>IsStockEnum::NO]);
        if(!empty($detail_ids)) {
            $query->andWhere(['id'=>$detail_ids]);
        }
        $models = $query->all();
        foreach ($models as $model){
            $buchan_status = BuChanEnum::INITIALIZATION;
            $goods = [
                    'goods_name' =>$model->goods_name,
                    'goods_num' =>$model->goods_num,
                    'from_order_id'=>$model->order_id,
                    'from_detail_id' => $model->id,
                    'from_order_sn'=>$order->order_sn,
                    'from_type' => FromTypeEnum::ORDER,
                    'style_sn' => $model->style_sn,
                    //'peiliao_type'=>$model->peiliao_type,
                    //'peishi_type'=>$model->peishi_type,
                    //'peishi_status'=>$peishi_status,
                    //'peiliao_status'=>$peiliao_status,
                    'bc_status' => $buchan_status,
                    'qiban_sn' => $model->qiban_sn,
                    'qiban_type'=>$model->qiban_type,
                    'jintuo_type'=>$model->jintuo_type,
                    'style_sex' =>$model->style_sex,
                    'is_inlay' =>$model->is_inlay,
                    'product_type_id'=>$model->product_type_id,
                    'style_cate_id'=>$model->style_cate_id,
                    //'supplier_id'=>$order->supplier_id,
                    //'follower_id'=>$order->follower_id,
                    'factory_mo'=>$model->factory_mo,
                    //'factory_distribute_time' => time()
            ];
            if($model->produce_id && $model->produce){
                if($model->produce->bc_status > BuChanEnum::IN_PRODUCTION) {
                    //生产中之后的流程，禁止同步
                    continue;
                }else {
                    unset($goods['bc_status']);
                    $goods['id'] = $model->produce->id;
                    //如果是配料中的，不同步配料类型和配料状态
                    if($model->produce->bc_status == BuChanEnum::IN_PEILIAO) {
                        /* unset($goods['peiliao_type']);
                        unset($goods['peishi_status']);
                        unset($goods['peiliao_status']); */
                    }
                }
            }
            $goods_attrs = OrderGoodsAttribute::find()->where(['id'=>$model->id])->asArray()->all();
            $produce = Yii::$app->supplyService->produce->createProduce($goods ,$goods_attrs);
            if($produce) {
                $model->produce_id = $produce->id;
            }
            if(false === $model->save()) {
                throw new \Exception($this->getError($model),422);
            }
        }
    }
    
    /**
     * 创建订单编号
     * @param Style $model
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
            ->select(['sum(goods_num) as total_num','sum(goods_price*goods_num) as total_goods_price','sum(goods_discount) as total_goods_discount','sum(goods_pay_price*goods_num) as total_pay_price','min(is_stock) as is_stock','min(is_gift) as is_gift'])
            ->where(['order_id'=>$order_id])
            ->asArray()->one();
        if($sum) {
            $order_type = $sum['is_stock'] == 1 ? 1 : 2; //1现货 2定制
            Order::updateAll(['goods_num'=>$sum['total_num'], 'order_type'=>$order_type],['id'=>$order_id]);
            $order_account = OrderAccount::find()->where(['order_id'=>$order_id])->one();
            if(empty($order_account)){
                $order_account = new OrderAccount();
                $order_account->order_id = $order_id;
            }
            $order_account->discount_amount = $sum['total_goods_discount'];
            $order_account->goods_amount = $sum['total_goods_price'];
            $order_account->order_amount = $sum['total_pay_price'];
            $order_account->pay_amount = $sum['total_pay_price'] - $order_account->paid_amount;
            $order_account->save();
        }
    }
    
}