<?php

namespace addons\Warehouse\common\forms;

use addons\Warehouse\common\models\WarehouseBill;
/**
 * 款式编辑-款式属性 Form
 *
 */
class WarehouseBillMForm extends WarehouseBill
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {      
         $rules = [
            [['to_warehouse_id'], 'required']
         ];
         return array_merge(parent::rules() , $rules);
    }

   
}
