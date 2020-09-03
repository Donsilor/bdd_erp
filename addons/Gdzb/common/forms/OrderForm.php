<?php

namespace addons\Gdzb\common\forms;

use common\enums\TargetTypeEnum;
use Yii;
use common\helpers\ArrayHelper;
use addons\Gdzb\common\models\Order;
use common\helpers\RegularHelper;
use addons\Gdzb\common\models\Customer;

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
