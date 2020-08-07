<?php

namespace addons\Sales\common\forms;

use common\enums\TargetTypeEnum;
use Yii;
use common\helpers\ArrayHelper;
use addons\Sales\common\models\Order;
use common\helpers\RegularHelper;
use addons\Sales\common\models\Customer;

/**
 * 订单 Form
 */
class OrderForm extends Order
{

    //审批流程
    public $targetType;
    
    public $customer_mobile_1;
    public $customer_mobile_2;
    public $customer_email_1;
    public $customer_email_2;
    public $customer_source;
    public $customer_level;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
                [['pay_type'],'required'],
                [['customer_mobile_1'],'required','isEmpty'=>function($value){
                    if($this->sale_channel_id != 3 && $value !== null && empty($value)) {
                        return true;//提示 为空 错误
                    }
                    return false;//不验证
                },
                'whenClient' => "function (attribute, value) {
                     if($('#orderform-sale_channel_id').val() != 3){
                          return true;//启用 必填 功能
                     }
                    return false;//禁用  必填 功能
                }"
                ],
                [['customer_email_2'],'required','isEmpty'=>function($value){
                    if($this->sale_channel_id == 3 && $value !== null && empty($value)) {
                        return true;
                    }
                    return false;
                },
                'whenClient' => "function (attribute, value) {
                     if($('#orderform-sale_channel_id').val() == 3){
                          return true;//启用 必填 功能
                     }
                     return false;//禁用  必填 功能
                }"
                ],
                [['customer_email_1','customer_email_2'], 'match', 'pattern' => RegularHelper::email(), 'message' => '邮箱地址不合法'],
                [['customer_mobile_1','customer_mobile_2'], 'string', 'max' => 30],
                [['customer_mobile_1','customer_email_2'],'buildCustomerInfo'],
                [['customer_source','customer_level'],'safe']
        ];
        return ArrayHelper::merge(parent::rules(),$rules);
    }    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        //合并
        return ArrayHelper::merge(parent::attributeLabels() , [
                'customer_mobile_1'=>'客户手机',
                'customer_mobile_2'=>'客户手机',
                'customer_email_1'=>'客户邮箱',
                'customer_email_2'=>'客户邮箱',
                'customer_source' => '客户来源',
                'customer_level' => '客户级别',
        ]);
    }
    /**
     * 格式化客户信息
     */
    public function buildCustomerInfo($attribute,$params) 
    {
        if($this->sale_channel_id == 3) {
            if($this->customer_email_2) {
                $exist = Customer::find()->where(['email'=>$this->customer_email_2,'channel_id'=>$this->sale_channel_id])->count();
                if(!$exist) {
                    $this->addError('customer_email_2',"客户邮箱不存在，请先添加客户");
                }
            }
            $this->customer_mobile = $this->customer_mobile_2;
            $this->customer_email = $this->customer_email_2;               
        }else if($this->sale_channel_id != 3){            
            $this->customer_mobile = $this->customer_mobile_1;
            $this->customer_email = $this->customer_email_1;
        }
    }
    /**
     * 验证客户邮箱
     */
    /* public function validateEmail()
    {
        if($this->sale_channel_id == 3 && $this->customer_mobile_2) {
            $exist = Customer::find()->where(['email'=>$this->customer_mobile_2,'channel_id'=>$this->sale_channel_id])->count();
            if(!$exist) {
                $this->addError('customer_mobile_2',"客户邮箱不存在，请先添加客户信息");
            }
        }
    } */
    public function getTargetType(){
        switch ($this->sale_channel_id){
            case 3:
                $this->targetType = TargetTypeEnum::ORDER_F_MENT;
                break;
            case 4:
                $this->targetType = TargetTypeEnum::ORDER_Z_MENT;
                break;
            case 9:
                $this->targetType = TargetTypeEnum::ORDER_T_MENT;
                break;
            default:
                $this->targetType = false;

        }
        
        return $this->targetType;
    }
    
}
