<?php

namespace addons\Sales\common\forms;

use common\enums\TargetTypeEnum;
use Yii;
use common\helpers\ArrayHelper;
use addons\Sales\common\models\Order;

/**
 * 订单 Form
 */
class OrderForm extends Order
{

    //审批流程
    public $targetType;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
             [['customer_mobile','customer_email'],'validateCustomer']
        ];
        return ArrayHelper::merge(parent::rules() , $rules);
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        //合并
        return ArrayHelper::merge(parent::attributeLabels() , [
                
        ]);
    }
    public function validateCustomer($attribute) 
    {
        if($this->sale_channel_id ==3 && $this->customer_email=='') {
            $this->addError($attribute,"客户邮箱必填");
            return false;
        }else if($this->sale_channel_id != 3 && $this->customer_mobile ==''){
            $this->addError($attribute,"客户手机必填");
            return false;
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
