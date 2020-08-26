<?php

namespace addons\Warehouse\common\forms;

use addons\Warehouse\common\models\WarehouseBill;
use common\helpers\ArrayHelper;

/**
 * 收货单 Form
 *
 */
class WarehouseBillTForm extends WarehouseBill
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
         $rules = [
            [['put_in_type', 'to_warehouse_id', 'supplier_id'], 'required']
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
            //'supplier_id' => '加工商',
            'creator_id' => '制单人',
            'created_at' => '制单时间',
        ]);
    }
}
