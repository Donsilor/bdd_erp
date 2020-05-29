<?php

namespace addons\Warehouse\common\forms;

use addons\Warehouse\common\models\WarehouseBill;
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

   
}
