<?php

namespace addons\Warehouse\common\forms;


use common\helpers\ArrayHelper;
use addons\Warehouse\common\models\WarehouseBillGoods;

/**
 * 其他收货单明细 Form
 *
 */
class WarehouseBillTGoodsForm extends WarehouseBillGoods
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
