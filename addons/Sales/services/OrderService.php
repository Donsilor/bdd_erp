<?php

namespace addons\Sales\services;

use addons\Purchase\common\enums\PurchaseCateEnum;
use addons\Purchase\common\enums\PurchaseGoodsTypeEnum;
use addons\Sales\common\models\OrderGoods;
use addons\Sales\common\models\OrderGoodsAttribute;
use addons\Style\common\enums\QibanTypeEnum;
use Yii;
use common\components\Service;
use common\helpers\Url;
use addons\Sales\common\forms\OrderForm;
use addons\Sales\common\models\OrderAccount;
use addons\Sales\common\models\Customer;
use addons\Sales\common\models\Order;
use addons\Sales\common\models\OrderAddress;
use common\enums\AuditStatusEnum;
use addons\Sales\common\enums\IsStockEnum;
use addons\Style\common\models\Style;
use addons\Finance\common\models\OrderPay;
use common\helpers\SnHelper;
use addons\Sales\common\enums\PayStatusEnum;
use common\enums\LogTypeEnum;
use addons\Sales\common\enums\OrderFromEnum;
use addons\Sales\common\models\OrderInvoice;
use addons\Sales\common\forms\ExternalOrderForm;
use addons\Sales\common\forms\OrderImportForm;
use common\helpers\UploadHelper;
use common\helpers\ExcelHelper;
use addons\Sales\common\models\Platform;
use common\enums\LanguageEnum;
use addons\Sales\common\forms\OrderImportKForm;
use addons\Sales\common\forms\OrderFullForm;
use common\helpers\ArrayHelper;

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
            $model = Order::find()->select(['id','order_from'])->where(['id'=>$order_id])->one();
            if($model->order_from == OrderFromEnum::FROM_EXTERNAL) {
                return [
                        1=>['name'=>'订单信息','url'=>Url::to(['external-order/view','id'=>$order_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                        2=>['name'=>'日志信息','url'=>Url::to(['order-log/index','order_id'=>$order_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                ];      
            }else {
                return [
                        1=>['name'=>'订单信息','url'=>Url::to(['order/view','id'=>$order_id,'tab'=>1,'returnUrl'=>$returnUrl])],
                        2=>['name'=>'日志信息','url'=>Url::to(['order-log/index','order_id'=>$order_id,'tab'=>2,'returnUrl'=>$returnUrl])],
                ];
            }
    } 
    /**
     * 人工创建订单
     * 
     * @param OrderForm $form
     */
    public function createOrder($form,$mode = 'form')
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
        if($order->customer_mobile) {
            $customer = Customer::find()->where(['mobile'=>$order->customer_mobile,'channel_id'=>$order->sale_channel_id])->one();
        }else if($order->customer_email) {
            $customer = Customer::find()->where(['email'=>$order->customer_email,'channel_id'=>$order->sale_channel_id])->one();
        }
        if(!$customer) {          
            //2.创建用户信息
            $customer = new Customer();
            $customer->realname = $order->customer_name;
            $customer->mobile = $order->customer_mobile;
            $customer->email = $order->customer_email;
            $customer->channel_id = $order->sale_channel_id;
            $customer->level = $form->customer_level;
            $customer->source_id = $form->customer_source;            
            if(false == $customer->save()) {
                throw new \Exception("创建用户失败：".$this->getError($customer));
            }
            \Yii::$app->salesService->customer->createCustomerNo($customer,true);
        }else{
            //更新用户信息
            $customer->realname = $customer->realname ? $customer->realname : $order->customer_name;
            $customer->mobile = $customer->mobile ? $customer->mobile: $order->customer_mobile;
            $customer->email = $customer->email ? $customer->email : $order->customer_email;
            $customer->level = $customer->level ? $customer->level: $form->customer_level;
            $customer->source_id = $customer->source_id ? $customer->source_id : $form->customer_source;
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

        if($isNewOrder === true){
            $account = new OrderAccount();
            $account->order_id = $order->id;
            $account->currency = $order->currency;
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
            $address->address_details = $customer->address;
        }        
        if(false == $address->save(false)) {
            throw new \Exception("同步收货地址失败：".$this->getError($address));
        }
        
        //商品金额汇总
        $this->orderSummary($order->id);
        
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
            \Yii::$app->salesService->orderLog->createOrderLog($log);
        }
        return $order;        
    }
    /**
     *  创建外部平台订单
     * @param ExternalOrderForm $form
     */
    public function createExternalOrder($form, $mode = 'form')
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        $isNewOrder = $form->isNewRecord;
        //1.创建订单
        $order = clone $form;
        $order->order_from = OrderFromEnum::FROM_EXTERNAL;
        if(empty($order->order_time)){
            if(!empty($order->pay_time)) {
                $order->order_time = $order->pay_time;
            }else{
                $order->order_time = time();
            }            
        }
        if(false == $order->save()) {
            throw new \Exception($this->getError($order));
        }        
        //2.创建订单明细
        if($isNewOrder === true){
            foreach ($form->goods_list ?? [] as $goods) {
                $orderGoods = new OrderGoods();
                $orderGoods->attributes = $goods;
                $orderGoods->order_id = $order->id;
                if(false === $orderGoods->save()) {
                    throw new \Exception($this->getError($orderGoods));
                }
            }
        }
        //3.创建订单金额
        $account = OrderAccount::find()->where(['order_id'=>$order->id])->one();
        if(!$account) {
            $account = new OrderAccount();
            $account->order_id = $order->id;
        }
        $account->other_fee = $form->other_fee;
        $account->arrive_amount = $form->arrive_amount;
        $account->currency = $form->currency;
        if(false == $account->save()) {
            throw new \Exception($this->getError($account));
        }
        
        //4.订单收货地址
        $address = OrderAddress::find()->where(['order_id'=>$order->id])->one();
        if(!$address) {
            $address = new OrderAddress();
            $address->order_id = $order->id;            
        }
        if($form->_platform) {
            $address->realname = $form->_platform->realname; 
            $address->mobile = $form->_platform->mobile; 
            $address->country_id = $form->_platform->country_id;
            $address->province_id = $form->_platform->province_id; 
            $address->city_id = $form->_platform->city_id;
            $address->zip_code = $form->_platform->zip_code;
            $address->address_details = $form->_platform->address_details;
        }      
        
        if(false == $address->save()) {
            throw new \Exception("同步收货地址失败：".$this->getError($address));
        }        
        
        if($form->isNewRecord){
            $order->order_sn = $this->createOrderSn($order);
        }
        if(false == $order->save()) {
            throw new \Exception($this->getError($order));
        }   
        //商品金额汇总
        $this->orderSummary($order->id);

        //创建订单日志
        if($isNewOrder === true) {
            if($mode == "import") {
                $log = [
                        'order_id' => $order->id,
                        'order_sn' => $order->order_sn,
                        'order_status' => $order->order_status,
                        'log_type' => LogTypeEnum::ARTIFICIAL,
                        'log_time' => time(),
                        'log_module' => '批量导入订单',
                        'log_msg' => "批量导入订单, 订单号:".$order->order_sn
                ];
                
            }else{
                $log = [
                        'order_id' => $order->id,
                        'order_sn' => $order->order_sn,
                        'order_status' => $order->order_status,
                        'log_type' => LogTypeEnum::ARTIFICIAL,
                        'log_time' => time(),
                        'log_module' => '创建订单',
                        'log_msg' => "创建订单, 订单号:".$order->order_sn
                ];
            }
            
            \Yii::$app->salesService->orderLog->createOrderLog($log);
        }
        return $order;
    }
    /**
     *  国际批发订单导入
     * @param OrderImportKForm $form
     */
    public function importOrderK($form)
    { 
        if (!($form->file->tempName ?? true)) {
            throw new \Exception("请上传文件");
        }
        if (UploadHelper::getExt($form->file->name) != 'xlsx') {
            throw new \Exception("请上传xlsx格式文件");
        }
       
        $startRow = 2;
        $endColumn = count($form->columns);
        $rows = ExcelHelper::import($form->file->tempName, $startRow, $endColumn, $form->columns);//从第1行开始,第4列结束取值
        if(!isset($rows[$startRow+1])) {
            throw new \Exception("导入数据不能为空");
        }
        //1.数据校验及格式化
        foreach ($rows as $rowIndex=> & $row) {
            if($rowIndex == $startRow) {
                $form->titles = $row;
                continue;
            }
            if(($form->titles['remark'] ?? '') != '商品备注') {
                throw new \Exception("数据模板有变动，请下载最新模板");
            }
            //加载表格行数据 并且数据校验
            if(empty(array_filter($row)) || false === $form->loadRow($row,$rowIndex)){
                continue;
            }
        }        
        //订单再次校验
        $form->validateOrders();
        
        if($form->hasError() === false) {
            foreach ($form->order_list as $k=>$order) {
                try{
                    $order = $this->createFullOrder($order);
                    
                    $log = [
                            'order_id' => $order->id,
                            'order_sn' => $order->order_sn,
                            'order_status' => $order->order_status,
                            'log_type' => LogTypeEnum::ARTIFICIAL,
                            'log_time' => time(),
                            'log_module' => '创建订单',
                            'log_msg' => "订单批量导入, 订单号:".$order->order_sn
                    ];                
                    \Yii::$app->salesService->orderLog->createOrderLog($log);
                }catch (\Exception $e) {
                    $form->addRowError($rowIndex, 'error', "创建订单失败：".$e->getMessage());
                } 
            }
        }
        $form->showImportMessage();
    }
    
    /**
     * 订单全量添加/更新
     * @param OrderFullForm $form
     */
    public function createFullOrder($form)
    {
        //1.订单
        $isNewOrder = false;
        $order = $form->order;
        if(!$order->id) {
            $isNewOrder = true;
        }
        if(false === $order->save()) {
            throw new \Exception($this->getError($order));
        }
        //2.同步订单金额
        $account = OrderAccount::find()->where(['order_id'=>$order->id])->one();
        if(!$account) {
            $account = $form->account;
            $account->order_id = $order->id;
        }else {
            $account->attributes = $form->account->toArray();
        }
        $account->currency = $order->currency;
        if(false == $account->save()) {
            throw new \Exception($this->getError($account));
        }
        if($account->paid_amount > 0) {
            //3.创建点款记录
            $orderPay = OrderPay::find()->where(['pay_sn'=>$order->pay_sn])->one();
            if(!$orderPay) {
                $orderPay = new OrderPay();
                $orderPay->order_id = $order->id;
                $orderPay->pay_sn = SnHelper::createOrderPaySn();
                $orderPay->pay_amount = $account->paid_amount;
                $orderPay->pay_type =  $order->pay_type;
                $orderPay->pay_status = PayStatusEnum::HAS_PAY;
                $orderPay->currency = $account->currency;
                $orderPay->exchange_rate = $account->exchange_rate;
                if(false === $orderPay->save()) {
                    throw new \Exception($this->getError($orderPay));
                }
            }
            $order->pay_sn = $orderPay->pay_sn;//点款单号            
        }
        
        //4.同步订单商品明细
        if($isNewOrder === true) {
            $goods_num = 0;//商品总数
            $goods_discount = 0;//商品优惠金额
            foreach ($form->goods_list ?? [] as $goodsInfo) {
                $style_sn = $goodsInfo['style_sn'] ?? '';
                $style = Style::find()->where(['style_sn'=>$style_sn])->one();
                if(!$style) {
                    $orderGoods = new OrderGoods();
                    $orderGoods->attributes = $goodsInfo;
                    $orderGoods->order_id = $order->id;
                }else {
                    //从款式信息自动带出款的信息
                    $styleInfo = $style->toArray(['style_name','style_cate_id','product_type_id','is_inlay','style_channel_id','style_sex']);
                    $orderGoods = new OrderGoods();
                    $orderGoods->attributes = $goodsInfo;
                    $orderGoods->jintuo_type = 1;
                    $orderGoods->style_cate_id = $style->style_cate_id;
                    $orderGoods->product_type_id = $style->product_type_id;
                    $orderGoods->is_inlay = $style->is_inlay;
                    $orderGoods->style_channel_id = $style->style_channel_id;
                    $orderGoods->style_sex = $style->style_sex;
                    $orderGoods->goods_name = $orderGoods->goods_name ? $orderGoods->goods_name : $style->style_name;
   
                    $orderGoods->order_id = $order->id;
                    if(empty($goodsInfo['goods_image'])) {
                        $orderGoods->goods_image = $style->style_image;
                    }
                }
                if(false === $orderGoods->save()) {
                    throw new \Exception("同步订单商品失败：".$this->getError($orderGoods));
                }
                /**
                 * [attr_id] => 6[attr_value_id] => 16[attr_value] => 圆形
                 */
                foreach ($goodsInfo['goods_attrs'] ??[] as $goods_attr) {
                    if(empty($goods_attr)){
                        continue;
                    }
                    $goodsAttr = new OrderGoodsAttribute();
                    $goodsAttr->attributes = $goods_attr;
                    if($goodsAttr->attr_value_id) {
                        $goodsAttr->attr_value = Yii::$app->attr->valueName($goodsAttr->attr_value_id);
                    }
                    $goodsAttr->id = $orderGoods->id;
                    if(false === $goodsAttr->save()) {
                          throw new \Exception("同步商品属性失败：".$this->getError($goodsAttr));
                    }
                }
                $goods_discount += $orderGoods->goods_discount;
                $goods_num += $orderGoods->goods_num;
            }
            $order->goods_num   = $goods_num;
            
            $account->goods_discount = $goods_discount;
            $account->order_discount = $account->discount_amount - $goods_discount;
            if(false === $account->save()) {
                throw new \Exception("同步订单金额失败：".$this->getError($account));
            }
        }
        //6.同步订单收货地址
        $address = OrderAddress::find()->where(['order_id'=>$order->id])->one();
        if(!$address) {
            $address = $form->address;
            $address->order_id = $order->id;
        }else {
            $address->attributes = $form->address->toArray();
        }       
        if(false == $address->save(false)) {
            throw new \Exception("同步收货地址失败：".$this->getError($address));
        } 
        //6.同步订单发票信息
        $invoice = OrderInvoice::find()->where(['order_id'=>$order->id])->one();
        if(!$invoice) {
            $invoice = $form->invoice;
            $invoice->order_id = $order->id;
        }
        $invoice->attributes = $form->invoice->toArray();
        if(false == $invoice->save()) {
            throw new \Exception("同步发票失败：".$this->getError($invoice));
        }      
        
        if($order->order_sn == ''){
            $order->order_sn = $this->createOrderSn($order);
        }
        if(false == $order->save()) {
            throw new \Exception($this->getError($order));
        }
        //商品金额汇总
        $this->orderSummary($order->id);
        return $order;
    }
    /**
     *  外部订单导入
     * @param OrderImportForm $form
     */
    public function importExternalOrder($form)
    {
        if (!($form->file->tempName ?? true)) {
            throw new \Exception("请上传文件");
        }
        if (UploadHelper::getExt($form->file->name) != 'xlsx') {
            throw new \Exception("请上传xlsx格式文件");
        }
        
        $startRow = 3;
        $endColumn = count($form->columns);
        
        $rows = ExcelHelper::import($form->file->tempName, $startRow, $endColumn, $form->columns);//从第1行开始,第4列结束取值
        if(!isset($rows[$startRow+1])) {
            throw new \Exception("导入数据不能为空");
        }
        $order_list = [];
        $error_flag = false;
        //1.数据校验及格式化
        foreach ($rows as $rowIndex=> & $row) {
            if($rowIndex == $startRow) {
                $form->titles = $row;
                continue;
            }
            //加载表格行数据 并且数据校验
            if(false === $form->loadRow($row,$rowIndex)){
                $error_flag = true;
                continue;
            }            
            
            $order = new ExternalOrderForm();
            $order->language = $form->language;
            $order->currency = $form->currency;
            $order->platform_id = $form->platform->id;
            $order->sale_channel_id = $form->platform->channel_id;
            $order->out_trade_no = $form->out_trade_no;            
            $order->pay_type = $form->platform->payment_id;
            $order->customer_mobile = $form->customer_mobile;
            $order->pay_remark = $form->pay_remark;
            $order->remark = $form->remark;
            $order->pay_time = $form->pay_time;//支付时间=下单时间
            $order->order_time = $form->pay_time;
            $order->platform_id = $form->platform->id;
            $order->_platform = $form->platform;//平台收货地址
            if($form->style_1) {
                $order->goods_list[] = [
                        'style_sn' =>$form->style_sn_1,
                        'goods_name'=>$form->goods_name_1,
                        'size' =>$form->size_1,
                        'finger_type' =>$form->finger_type_1,
                        'goods_price'=>$form->goods_price_1
                ];
            }
            if($form->style_2) {
                $order->goods_list[] = [
                        'style_sn' =>$form->style_sn_2,
                        'goods_name'=>$form->goods_name_2,
                        'size' =>$form->size_2,
                        'finger_type' =>$form->finger_type_2,
                        'goods_price'=>$form->goods_price_2
                ];
            }                        
            $order_list[$rowIndex] = $order;
        }
        if($error_flag === false) {
            foreach ($order_list as $rowIndex=>$order) {
                try{
                     $this->createExternalOrder($order, 'import');
                }catch (\Exception $e) {
                     $form->addRowError($rowIndex, 'error', "创建订单失败：".$e->getMessage());
                }
            } 
        }
        $form->showImportMessage();
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
    public function syncOrder($orderInfo, $accountInfo, $goodsList, $customerInfo, $addressInfo, $invoiceInfo = [])
    {
        if(empty($orderInfo['out_trade_no'])) {
            throw new \Exception("orderInfo->out_trade_no 不能为空");
        }
        //1.同步订单
        $isNewOrder = false;
        $order = Order::find()->where(['out_trade_no'=>$orderInfo['out_trade_no']])->one();
        if(!$order) {
            $isNewOrder = true;
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
        
        //3.创建点款记录
        $orderPay = OrderPay::find()->where(['pay_sn'=>$order->pay_sn])->one();
        if(!$orderPay) {
            $orderPay = new OrderPay();
            $orderPay->order_id = $order->id;
            $orderPay->pay_sn = SnHelper::createOrderPaySn();
            $orderPay->pay_amount = $account->paid_amount;
            $orderPay->pay_type =  $order->pay_type;
            $orderPay->pay_status = PayStatusEnum::HAS_PAY;
            $orderPay->currency = $account->currency;
            $orderPay->exchange_rate = $account->exchange_rate;
            $orderPay->creator_id = 0;
            $orderPay->creator = "system";
            if(false === $orderPay->save()) {
                throw new \Exception($this->getError($orderPay));
            }
        }
        $order->pay_sn = $orderPay->pay_sn;//点款单号
        if($account->paid_amount < $account->pay_amount) {
            $order->pay_status  = PayStatusEnum::PART_PAY;
        }
        //4.同步订单商品明细
        if($isNewOrder === true) {
            $goods_num = 0;//商品总数
            $goods_discount = 0;//商品优惠金额
            foreach ($goodsList as $goodsInfo) {
                $style_sn = $goodsInfo['style_sn'] ?? '';
                $style = Style::find()->where(['style_sn'=>$style_sn])->one();
                if(!$style) {
                    //throw new \Exception("[{$style_sn}]款号在erp系统不存在");
                    $orderGoods = new OrderGoods();
                    $orderGoods->attributes = $goodsInfo;
                    $orderGoods->order_id = $order->id;
                }else {
                    //从款式信息自动带出款的信息
                    $styleInfo = $style->toArray(['style_cate_id','product_type_id','is_inlay','style_channel_id','style_sex']);
                    $orderGoods = new OrderGoods();
                    $orderGoods->attributes = $goodsInfo + $styleInfo;
                    $orderGoods->order_id = $order->id;
                    if(empty($goodsInfo['goods_image'])) {
                        $orderGoods->goods_image = $style->style_image;
                    }
                }
                if(false === $orderGoods->save()) {
                    throw new \Exception("同步订单商品失败：".$this->getError($orderGoods));
                }
                /**
                 * [attr_id] => 6[attr_value_id] => 16[attr_value] => 圆形
                 */
                foreach ($goodsInfo['goods_attrs'] ??[] as $goods_attr) {
                    $goodsAttr = new OrderGoodsAttribute();
                    $goodsAttr->attributes = $goods_attr;
                    if($goodsAttr->attr_value_id) {
                        $goodsAttr->attr_value = Yii::$app->attr->valueName($goodsAttr->attr_value_id);
                    }
                    $goodsAttr->id = $orderGoods->id;
                    if(false === $goodsAttr->save()) {
                        throw new \Exception("同步商品属性失败：".$this->getError($goodsAttr));
                    }
                }
                $goods_discount += $orderGoods->goods_discount;
                $goods_num += $orderGoods->goods_num;
            }
            $order->goods_num   = $goods_num;

            $account->goods_discount = $goods_discount;
            $account->order_discount = $account->discount_amount - $goods_discount;
            if(false === $account->save()) {
                throw new \Exception("同步订单金额失败：".$this->getError($account));
            }            
        }
        //5.同步客户信息
        if($order->customer_mobile) {
            $customer = Customer::find()->where(['mobile'=>$order->customer_mobile,'channel_id'=>$order->sale_channel_id])->one();
        }else if($order->customer_email) {
            $customer = Customer::find()->where(['email'=>$order->customer_email,'channel_id'=>$order->sale_channel_id])->one();
        }
        if(!$customer) {
            //2.创建用户信息
            $customer = new Customer();
            $customer->attributes = $customerInfo;
            $customer->channel_id = $order->sale_channel_id;
            if(false == $customer->save()) {
                throw new \Exception("创建用户失败：".$this->getError($customer));
            }
            \Yii::$app->salesService->customer->createCustomerNo($customer,true);
        }else{
            //更新用户信息
            //$customer->realname = $customer->realname ? $customer->realname : $order->customer_name;
            //$customer->mobile = $customer->mobile ? $customer->mobile: $order->customer_mobile;
            //$customer->email = $customer->email ? $customer->email : $order->customer_email;
            $customer->attributes = $customerInfo;
            if(false == $customer->save()) {
                throw new \Exception("更新用户失败：".$this->getError($customer));
            }
        }        
        //6.同步订单收货地址
        $address = OrderAddress::find()->where(['order_id'=>$order->id])->one();
        if(!$address) {
            $address = new OrderAddress();            
            $address->order_id = $order->id;
        }
        $address->attributes = $addressInfo;     
        if(false == $address->save()) {
            throw new \Exception("同步收货地址失败：".$this->getError($address));
        }  
        //7.同步发票
        if(!empty($invoiceInfo)) {
            $invoice = OrderInvoice::find()->where(['order_id'=>$order->id])->one();
            if(!$invoice) {
                $invoice = new OrderInvoice();
                $invoice->order_id = $order->id;
            }
            $invoice->attributes = $invoiceInfo;
            if(false == $invoice->save()) {
                throw new \Exception("同步发票失败：".$this->getError($invoice));
            }
            $order->is_invoice   = $invoice->is_invoice;
        }
        
        $order->customer_id = $customer->id;
        if($order->order_sn == ''){
            $order->order_sn = $this->createOrderSn($order);
        }
        if(false == $order->save()) {
            throw new \Exception($this->getError($order));
        }
        //商品金额汇总
        $this->orderSummary($order->id);
        //创建订单日志
        if($isNewOrder === true) {
            $log = [
                    'order_id' => $order->id,
                    'order_sn' => $order->order_sn,
                    'order_status' => $order->order_status,
                    'log_type' => LogTypeEnum::SYSTEM,
                    'log_time' => time(),
                    'log_module' => '外部订单同步',
                    'log_msg' => "同步创建订单,订单号:".$order->order_sn.', 同步来源：'.OrderFromEnum::getValue($order->order_from).', 外部订单号:'.$order->out_trade_no
            ];
            \Yii::$app->salesService->orderLog->createOrderLog($log);
        }
        
        return $order;        
    }
    /**
     * 同步订单商品生成采购申请单
     * @param int $order_id
     * @param array|int $detail_ids
     */
    public function syncPurchaseApply($order_id, $detail_ids = null)
    {
        $applyInfo = [];
        $applyGoodsList = [];
        
        $order = Order::find()->where(['id'=>$order_id])->one();
        if($order->goods_num <= 0 ){
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
            $style = Style::find()->where(['style_sn'=>$model->style_sn])->one();
            $goods = [
                    'order_detail_id' =>$model->id,
                    'goods_image'=>$model->goods_image,
                    'goods_images'=>$model->goods_image,
                    'goods_name' =>$model->goods_name,
                    'goods_num' =>$model->goods_num,
                    'style_sn' => $model->style_sn,
                    'style_id' => $style->id,
                    'qiban_sn' => $model->qiban_sn,
                    'qiban_type'=>$model->qiban_type,
                    'jintuo_type'=>$model->jintuo_type,
                    'goods_type' => $model->qiban_type == QibanTypeEnum::NO_STYLE ? PurchaseGoodsTypeEnum::OTHER : PurchaseGoodsTypeEnum::STYLE,
                    'style_sex' =>$model->style_sex,
                    'is_inlay' =>$model->is_inlay,
                    'product_type_id'=>$model->product_type_id,
                    'style_cate_id'=>$model->style_cate_id,
                    'cost_price' => Yii::$app->salesService->orderGoods->getCostPrice($model),
                    'style_channel_id' => $model->style_channel_id,
                    'remark' => $model->remark,


            ];            
            $goods['goods_attrs'] = OrderGoodsAttribute::find()->where(['id'=>$model->id])->asArray()->all();
            $applyGoodsList[] = $goods;
        }


        //采购申请单头
        $applyInfo['order_sn'] = $order->order_sn;
        $applyInfo['purchase_cate'] = PurchaseCateEnum::ORDER;
        $applyInfo['channel_id'] = $order->sale_channel_id;
        //同步采购申请单
        $apply = Yii::$app->purchaseService->apply->createSyncApply($applyInfo, $applyGoodsList);

        //更新订单申请单ID
        $order->apply_id = $apply->id;
        if(false === $order->save(true,['apply_id'])) {
            throw new \Exception($this->getError($order));
        }
        return $apply;
    }
    /**
     * 同步商品属性
     * @param unknown $wareId
     * @param unknown $goods_spec
     * @return boolean
     */
    public function syncOrderGoodsAttr($wareId, $goods_attrs,$order_ids = [])
    {
        $orderGoodsList = OrderGoods::find()->select(['id'])->where(['out_ware_id'=>$wareId])->andFilterWhere(['in','order_id',$order_ids])->limit(1000)->all();
        if(empty($orderGoodsList)) {
             throw new \Exception("[{$wareId}] 查询不到记录");
        }
        foreach ($orderGoodsList as $orderGoods) {
            foreach ($goods_attrs ??[] as $goods_attr) {
                if(empty($goods_attr['attr_id'])) {
                    throw new \Exception("同步商品属性失败：attr_id 不能为空");
                }
                $model = OrderGoodsAttribute::find()->where(['id'=>$orderGoods->id,'attr_id'=>$goods_attr['attr_id']])->one();
                if(!$model){
                    $model = new OrderGoodsAttribute(); 
                }
                $model->attributes = $goods_attr;
                if($model->attr_value_id) {
                    $model->attr_value = Yii::$app->attr->valueName($model->attr_value_id);
                }
                $model->id = $orderGoods->id;
                if(false === $model->save()) {
                    throw new \Exception("同步商品属性失败：".$this->getError($model));
                }
            }
        }
        
    }
    /**
     * 同步商品规格
     * @param unknown $wareId
     * @param unknown $goods_spec
     * @return boolean
     */
    public function syncOrderGoodsSpec($wareId,$goods_spec) 
    {
        if(is_array($goods_spec)) {
            $goods_spec = json_encode($goods_spec);
        }else{
            return false;
        }
        return OrderGoods::updateAll(['goods_spec'=>$goods_spec],['out_ware_id'=>$wareId]);
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
        $order_time = $model->order_time ? $model->order_time: time();        
        $order_sn = date("Ymd",$order_time).str_pad($model->id,8,'0',STR_PAD_LEFT);
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
            $account = OrderAccount::find()->where(['order_id'=>$order_id])->one();
            if(empty($account)){
                $account = new OrderAccount();
                $account->order_id = $order_id;
            }
            $account->discount_amount = $sum['total_goods_discount'];
            $account->goods_amount = $sum['total_goods_price'];
            $account->order_amount = $account->goods_amount + $account->shipping_fee + $account->tax_fee + $account->safe_fee
                        + $account->other_fee; // 商品总金额+运费，税费，保险费
            $account->pay_amount = $account->order_amount - $account->discount_amount;
            if(false === $account->save()){
                throw new \Exception("订单金额汇总失败:".$this->getError($account));
            }
            //更新订单信息
            $order_type = $sum['is_stock'] == 1 ? 1 : 2; //1现货 2定制
            $order_data = ['goods_num'=>$sum['total_num'], 'order_type'=> $order_type];
            if($account->paid_amount < $account->pay_amount) {
                $order_data['pay_status']  = PayStatusEnum::PART_PAY;
            }else {
                $order_data['pay_status']  = PayStatusEnum::HAS_PAY;
            }
            Order::updateAll($order_data,['id'=>$order_id]);
            
        }
    }
    
}