<?php

namespace addons\Finance\common\forms;

use Yii;
use common\helpers\ArrayHelper;
use addons\Sales\common\models\Order;

/**
 * 订单点款 Form
 */
class OrderPayForm extends Order
{
    public $paid_amount;
    public $arrive_type;
    public $arrival_time;
    public $remark;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
             [['paid_amount','currency','pay_type','order_sn','arrive_type'],'required'],
             [['paid_amount'],'number'], 
             [['paid_amount'],'validateAmount'],
             ['arrival_time','safe'],
             [['remark'], 'string', 'max' => 255],
        ];
        return ArrayHelper::merge(parent::rules() , $rules);
    }
    /**
     * 实际支付金额校验
     * @param unknown $attribute
     */
    public function validateAmount($attribute)
    {
        if($this->paid_amount != $this->account->pay_amount) {
            $this->addError($attribute,"实际支付金额与应付金额不相符");
            return false;
        }
        return true;
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        //合并
        return ArrayHelper::merge(parent::attributeLabels() , [
                'paid_amount'=>'实际支付金额',
                'arrive_type' => '到账方式',
                'arrival_time' => '预估到账时间',
                'remark' => '备注',
        ]);
    }
    
    
}
