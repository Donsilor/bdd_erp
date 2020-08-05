<?php

namespace addons\Warehouse\common\forms;

use Yii;
use addons\Warehouse\common\models\WarehouseGoldBill;
use common\helpers\ArrayHelper;

/**
 * 单据列表 Form
 *
 */
class WarehouseGoldBillForm extends WarehouseGoldBill
{
    public $gold_sn;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
         $rules = [
             [['gold_sn'], 'string', 'max'=>30],
             [['gold_sn'], 'filter', 'filter' => 'trim'],
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
            'gold_sn' => '批次号',
        ]);
    }

   
}
