<?php

namespace addons\Warehouse\common\forms;

use addons\Warehouse\common\models\WarehouseStone;
use addons\Warehouse\common\models\WarehouseStoneBill;
use common\helpers\ArrayHelper;

/**
 * 石包单据 Form
 *
 */
class WarehouseStoneBillMsForm extends WarehouseStoneBill
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
         $rules = [
             [['created_at'], 'integer'],
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
            'delivery_no' => '采购收货单号',
            'creator_id' => '制单人',
            'created_at' => '制单时间',
        ]);
    }

   
}
