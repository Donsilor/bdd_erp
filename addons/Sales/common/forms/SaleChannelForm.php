<?php

namespace addons\Sales\common\forms;

use Yii;
use addons\Sales\common\models\Salechannel;
use common\helpers\ArrayHelper;

/**
 * 销售渠道 Form
 */
class SaleChannelForm extends Salechannel
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
