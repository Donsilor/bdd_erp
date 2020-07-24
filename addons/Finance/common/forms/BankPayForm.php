<?php

namespace addons\Finance\common\forms;

use addons\Finance\common\models\BankPay;
use common\enums\TargetTypeEnum;
use Yii;
use common\helpers\ArrayHelper;

/**
 * 订单 Form
 */
class BankPayForm extends BankPay
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
