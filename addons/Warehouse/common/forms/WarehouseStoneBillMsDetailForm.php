<?php

namespace addons\Warehouse\common\forms;

use addons\Warehouse\common\models\WarehouseStone;
use addons\Warehouse\common\models\WarehouseStoneBill;
use common\helpers\ArrayHelper;

/**
 * 石包单据 Form
 *
 */
class WarehouseStoneBillMsDetailForm extends WarehouseStoneBill
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
         $rules = [

         ];
         return ArrayHelper::merge(parent::rules() , $rules);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        //合并
        //return ArrayHelper::merge(parent::attributeLabels() , [
        //]);
    }

   
}
