<?php

namespace addons\Warehouse\common\forms;

use addons\Warehouse\common\models\WarehouseBill;
/**
 * 款式编辑-款式属性 Form
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
            [['deliver_goods_no', 'to_warehouse_id', 'supplier_id'], 'required']
         ];
         return array_merge(parent::rules() , $rules);
    }

   
}
