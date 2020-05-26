<?php

namespace addons\Warehouse\common\forms;

use addons\Warehouse\common\models\WarehouseBillRepair;

/**
 * 维修单 Form
 *
 */
class WarehouseBillRepairForm extends WarehouseBillRepair
{

    /**
     * {@inheritdoc}
     */
    public function rules()
    {      
         $rules = [];
         return array_merge(parent::rules() , $rules);
    }
}
