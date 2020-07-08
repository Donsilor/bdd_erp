<?php

namespace addons\Sales\services;

use Yii;
use common\components\Service;
use common\helpers\Url;
use addons\Sales\common\forms\OrderForm;
use addons\Sales\common\models\OrderAccount;
use addons\Sales\common\models\Customer;
use addons\Sales\common\models\Order;
use addons\Sales\common\models\OrderAddress;

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
    
}