<?php

namespace addons\Sales\common\forms;

use Yii;
use addons\Sales\common\models\ExpressArea;
use common\helpers\ArrayHelper;

/**
 * 物流配送区域 Form
 */
class ExpressAreaForm extends ExpressArea
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
