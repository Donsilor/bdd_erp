<?php

namespace addons\Warehouse\common\forms;

use common\helpers\ArrayHelper;
use addons\Warehouse\common\models\WarehouseBillGoods;
use common\helpers\StringHelper;

/**
 * 单据明细 Form
 *
 */
class WarehouseBillGoodsForm extends WarehouseBillGoods
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [

        ];
        return array_merge(parent::rules() , $rules);
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
