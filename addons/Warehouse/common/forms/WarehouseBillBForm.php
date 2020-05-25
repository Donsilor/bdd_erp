<?php

namespace addons\Warehouse\common\forms;

use addons\Warehouse\common\models\WarehouseBill;
/**
 * 退货返厂单 Form
 *
 */
class WarehouseBillBForm extends WarehouseBill
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {      
         $rules = [
            [['put_in_type', 'supplier_id'], 'required']
         ];
         return array_merge(parent::rules() , $rules);
    }

   
}
