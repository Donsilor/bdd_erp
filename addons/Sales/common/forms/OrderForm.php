<?php

namespace addons\Sales\common\forms;

use Yii;
use common\helpers\ArrayHelper;
use addons\Sales\common\models\Order;

/**
 * 订单 Form
 */
class OrderForm extends Order
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
