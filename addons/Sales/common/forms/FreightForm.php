<?php

namespace addons\Sales\common\forms;

use Yii;
use addons\Sales\common\models\Freight;
use common\helpers\ArrayHelper;

/**
 * 快递单 Form
 */
class FreightForm extends Freight
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
