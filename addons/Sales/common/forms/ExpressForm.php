<?php

namespace addons\Sales\common\forms;

use Yii;
use addons\Sales\common\models\Express;
use common\helpers\ArrayHelper;

/**
 * 物流快递 Form
 */
class ExpressForm extends Express
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
