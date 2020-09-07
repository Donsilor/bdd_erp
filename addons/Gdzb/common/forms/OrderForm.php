<?php

namespace addons\Gdzb\common\forms;

use Yii;
use common\helpers\ArrayHelper;
use addons\Gdzb\common\models\Order;


/**
 * 订单 Form
 */
class OrderForm extends Order
{

    public $customer_source;
    public $customer_level;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [

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

        ]);
    }


    
}
