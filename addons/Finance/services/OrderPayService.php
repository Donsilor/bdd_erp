<?php

namespace addons\Finance\services;

use addons\Sales\common\enums\DistributeStatusEnum;
use common\helpers\Url;
use common\components\Service;
use addons\Finance\common\forms\OrderPayForm;
use addons\Sales\common\enums\PayStatusEnum;
use addons\Finance\common\models\OrderPay;
use common\helpers\SnHelper;

/**
 * Class OrderPayService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class OrderPayService extends Service
{
    /**
     * 采购单菜单
     * @param int $id 款式ID
     * @return array
     */
    public function menuTabList($id, $returnUrl = null)
    {
        return [
                1=>['name'=>'基础信息','url'=>Url::to(['order-pay/view','id'=>$id,'tab'=>1,'returnUrl'=>$returnUrl])],
                3=>['name'=>'日志信息','url'=>Url::to(['order-pay/log','id'=>$id, 'tab'=>3,'returnUrl'=>$returnUrl])]
        ];
    }
    
    /**
     * 订单点款
     * @param OrderPayForm $form
     */
    public function pay($form) 
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }      
        if($form->account->paid_amount == $form->account->pay_amount) {
            throw new \Exception("订单已完成点款");
        }
        
        $form->account->paid_amount = $form->paid_amount;     
        if(false === $form->account->save()) {
            throw new \Exception($this->getError($form->account));
        }         
        //点款日志写入
        $orderPay = new OrderPay();
        $orderPay->order_id = $form->id;
        $orderPay->pay_sn = SnHelper::createOrderPaySn();
        $orderPay->pay_amount = $form->paid_amount;
        $orderPay->pay_type =  $form->pay_type;
        $orderPay->pay_status = PayStatusEnum::HAS_PAY;
        $orderPay->currency = $form->account->currency;
        $orderPay->exchange_rate = $form->account->exchange_rate;
        $orderPay->creator_id = \Yii::$app->user->id;
        $orderPay->creator = \Yii::$app->user->identity->username;
        if(false === $orderPay->save()) {
            throw new \Exception($this->getError($orderPay));
        }
        
        //订单变更
        $form->pay_sn = $orderPay->pay_sn;
        $form->pay_status = PayStatusEnum::HAS_PAY;
        $form->distribute_status = DistributeStatusEnum::ALLOWED;
        $form->pay_time   = time();
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }        
        
        return $orderPay;        
    }
    
    
}