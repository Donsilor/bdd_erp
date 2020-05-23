<?php

namespace addons\Warehouse\common\forms;

use addons\Warehouse\common\models\WarehouseBillGoods;

/**
 * 调拨单明细 Form
 *
 */
class WarehouseBillMGoodsForm extends WarehouseBillGoods
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
    
}
