<?php

namespace addons\Warehouse\common\forms;

use addons\Warehouse\common\models\WarehouseStoneBill;
use common\helpers\ArrayHelper;

/**
 * 石包单据 Form
 *
 */
class WarehouseStoneBillSsForm extends WarehouseStoneBill
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
         $rules = [
             [['supplier_id'], 'required'],
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
            
        ]);
    }

}
