<?php

namespace addons\Sales\common\forms;

use Yii;
use addons\Sales\common\models\CustomerSources;
use common\helpers\ArrayHelper;

/**
 * 客户来源 Form
 */
class CustomerSourcesForm extends CustomerSources
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
