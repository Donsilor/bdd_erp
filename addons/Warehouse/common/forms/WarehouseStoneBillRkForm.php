<?php

namespace addons\Warehouse\common\forms;

use addons\Warehouse\common\models\WarehouseStone;
use addons\Warehouse\common\models\WarehouseStoneBill;
use common\helpers\ArrayHelper;

/**
 * 石包单据 Form
 *
 */
class WarehouseStoneBillRkForm extends WarehouseStoneBill
{

    public $file;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
         $rules = [
             [['supplier_id'], 'required'],
             [['file'], 'file', 'extensions' => ['xlsx']],//'skipOnEmpty' => false,
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
            'total_num' => '石包总数',
            'creator_id' => '制单人',
            'created_at' => '制单时间',
            'file' => '上传石料明细',
        ]);
    }

   
}
