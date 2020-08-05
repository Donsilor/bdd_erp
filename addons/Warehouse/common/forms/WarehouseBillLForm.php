<?php

namespace addons\Warehouse\common\forms;

use addons\Warehouse\common\models\WarehouseBill;
use common\helpers\ArrayHelper;

/**
 * 收货单 Form
 *
 */
class WarehouseBillLForm extends WarehouseBill
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {      
         $rules = [
            [['put_in_type', 'send_goods_sn', 'to_warehouse_id', 'supplier_id'], 'required']
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
            'creator_id' => '制单人',
            'created_at' => '制单时间',
            'send_goods_sn' => '采购收货单号'
        ]);
    }
}
