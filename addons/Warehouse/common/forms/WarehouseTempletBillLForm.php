<?php

namespace addons\Warehouse\common\forms;

use Yii;
use addons\Warehouse\common\models\WarehouseTempletBill;
use common\helpers\ArrayHelper;

/**
 * 样板单据 Form
 *
 */
class WarehouseTempletBillLForm extends WarehouseTempletBill
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
         $rules = [
             [['supplier_id', 'channel_id'], 'required'],
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
            'delivery_no' => '送货单号',
            'creator_id' => '制单人',
            'created_at' => '制单时间',
        ]);
    }

   
}
