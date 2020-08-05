<?php

namespace addons\Sales\common\forms;

use Yii;
use addons\Sales\common\models\Payment;
use common\helpers\ArrayHelper;

/**
 * 支付方式 Form
 */
class PaymentForm extends Payment
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
