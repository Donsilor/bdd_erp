<?php

namespace addons\Sales\common\forms;

use common\enums\TargetTypeEnum;
use Yii;
use common\helpers\ArrayHelper;
use addons\Sales\common\models\Order;
use common\helpers\RegularHelper;

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
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
                [['customer_mobile_1'],'required','isEmpty'=>function($value){
                    if($this->sale_channel_id != 3) {
                        if($value == '') {
                            return true;
                        }
                    }
                }],
                [['customer_email_2'],'required','isEmpty'=>function($value){
                    if($this->sale_channel_id == 3 ) {
                        if($value == '') {
                            return true;
                        }
                    }
                }],
                [['customer_email_1','customer_email_2'], 'match', 'pattern' => RegularHelper::email(), 'message' => '邮箱地址不合法'],
                [['customer_mobile_1','customer_mobile_2'], 'string', 'max' => 30],
                [['customer_mobile_1','customer_email_2'],'buildCustomerInfo']
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
                'customer_email_2'=>'客户邮箱'
        ]);
    }
    
    public function buildCustomerInfo() 
    {
        if($this->sale_channel_id == 3) {
            $this->customer_mobile = $this->customer_mobile_2;
            $this->customer_email = $this->customer_email_2;               
        }else if($this->sale_channel_id != 3){            
            $this->customer_mobile = $this->customer_mobile_1;
            $this->customer_email = $this->customer_email_1;
        }
    }
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
    }
    
}
