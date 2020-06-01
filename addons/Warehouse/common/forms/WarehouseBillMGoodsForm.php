<?php

namespace addons\Warehouse\common\forms;

use addons\Warehouse\common\models\WarehouseBillGoods;
use common\helpers\ArrayHelper;


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


    public function attributeLabels()
    {
        //合并
        return ArrayHelper::merge(parent::attributeLabels() , [
            'from_warehouse_id'=>'出库仓库',
            'to_warehouse_id'=>'入库仓库',
            'material'=>'材质',
        ]);
    }
}
