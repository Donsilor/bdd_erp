<?php

namespace addons\Warehouse\common\forms;

use addons\Warehouse\common\models\WarehouseStone;
use addons\Warehouse\common\models\WarehouseStoneBill;
use common\helpers\ArrayHelper;

/**
 * 石包单据 Form
 *
 */
class WarehouseStoneBillForm extends WarehouseStoneBill
{
    public $stone_sn;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
         $rules = [
             [['stone_sn'], 'string', 'max'=>30],
             [['stone_sn'], 'filter', 'filter' => 'trim'],
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
            'stone_sn' => '石料编号',
        ]);
    }

}
