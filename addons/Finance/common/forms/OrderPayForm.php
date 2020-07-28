<?php

namespace addons\Finance\common\forms;

use Yii;
use common\helpers\ArrayHelper;
use addons\Finance\common\models\OrderPay;

/**
 * 订单点款 Form
 */
class OrderPayForm extends OrderPay
{
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
    
    
}
