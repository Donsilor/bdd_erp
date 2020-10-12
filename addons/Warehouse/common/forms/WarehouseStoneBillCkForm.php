<?php

namespace addons\Warehouse\common\forms;

use addons\Warehouse\common\models\WarehouseStone;
use addons\Warehouse\common\models\WarehouseStoneBill;
use common\helpers\ArrayHelper;

/**
 * 石包单据 Form
 *
 */
class WarehouseStoneBillCkForm extends WarehouseStoneBill
{

    public $file;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
         $rules = [
             [['supplier_id','out_type'], 'required'],
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
