<?php

namespace addons\Finance\common\forms;

use addons\Finance\common\models\BankPay;
use addons\Finance\common\models\BorrowPay;
use common\enums\TargetTypeEnum;
use Yii;
use common\helpers\ArrayHelper;

/**
 * 订单 Form
 */
class BorrowPayForm extends BorrowPay
{

    //审批流程
    public $targetType;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
                
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

    public function getTargetType(){
        if(in_array($this->dept_id,[17])){
            $this->targetType = TargetTypeEnum::BORROW_PAY_F_MENT;
        }elseif (in_array($this->dept_id,[6,7,8,9,10,11,12,13,14,15,16])){
            $this->targetType = TargetTypeEnum::BORROW_PAY_T_MENT;
        }else{
            $this->targetType = false;
        }
    }
    
}
