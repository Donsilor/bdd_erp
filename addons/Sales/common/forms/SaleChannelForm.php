<?php

namespace addons\Sales\common\forms;

use Yii;
use addons\Sales\common\models\SaleChannel;
use common\helpers\ArrayHelper;

/**
 * 销售渠道 Form
 */
class SaleChannelForm extends SaleChannel
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
