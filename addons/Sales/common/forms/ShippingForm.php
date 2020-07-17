<?php

namespace addons\Sales\common\forms;

use Yii;
use common\helpers\ArrayHelper;
use addons\Sales\common\models\Freight;

/**
 * 订单发货 Form
 */
class ShippingForm extends Freight
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            //[['order_sn'], 'required'],
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
