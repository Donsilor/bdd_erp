<?php

namespace addons\Finance\common\forms;

use addons\Finance\common\models\OrderPay;
use Yii;
use common\helpers\ArrayHelper;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
/**
 * 订单点款 Form
 */
class PayConfirmForm extends OrderPay
{
    public $arrival_amount;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            ['arrival_amount','required'],
            [['arrival_amount'],'validateAmount'],
            ['arrival_time','safe'],
        ];
        return ArrayHelper::merge(parent::rules() , $rules);
    }


    public function beforeSave($insert)
    {
        $this->arrival_time = strtotime($this->arrival_time);
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }


    /**
     * 实际支付金额校验
     * @param unknown $attribute
     */
    public function validateAmount($attribute)
    {
        if($this->arrival_amount != $this->pay_amount) {
            $this->addError($attribute,"到账金额与应付金额不相符");
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
            'arrival_amount'=>'到账金额',
        ]);
    }
    
    
}
