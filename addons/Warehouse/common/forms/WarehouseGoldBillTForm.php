<?php

namespace addons\Warehouse\common\forms;

use Yii;
use addons\Warehouse\common\models\WarehouseGoldBill;
use common\helpers\ArrayHelper;

/**
 * 金料单据 Form
 *
 */
class WarehouseGoldBillTForm extends WarehouseGoldBill
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
