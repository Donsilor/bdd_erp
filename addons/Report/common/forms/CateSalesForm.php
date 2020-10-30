<?php

namespace addons\Report\common\forms;

use Yii;
use addons\Sales\common\models\Order;
use common\helpers\ArrayHelper;

/**
 * 分渠道产品销量统计 Form
 */
class CateSalesForm extends Order
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [

        ];
        return ArrayHelper::merge(parent::rules(), $rules);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        //合并
        return ArrayHelper::merge(parent::attributeLabels(), [

        ]);
    }
}
