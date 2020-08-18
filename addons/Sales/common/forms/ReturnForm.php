<?php

namespace addons\Sales\common\forms;

use Yii;
use addons\Sales\common\models\SalesReturn;
use common\helpers\ArrayHelper;

/**
 * 退款单 Form
 */
class ReturnForm extends SalesReturn
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
