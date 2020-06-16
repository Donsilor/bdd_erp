<?php

namespace addons\Warehouse\common\forms;

use common\helpers\ArrayHelper;
use addons\Warehouse\common\models\WarehouseGoldBill;
/**
 * 盘点  Form
 *
 */
class WarehouseGoldBillWForm extends WarehouseGoldBill
{
    public $gold_sn;
    public $gold_weight;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
                [['warehouse'], 'required']
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
