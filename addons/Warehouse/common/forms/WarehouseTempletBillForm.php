<?php

namespace addons\Warehouse\common\forms;

use Yii;
use addons\Warehouse\common\models\WarehouseTempletBill;
use common\helpers\ArrayHelper;

/**
 * 单据列表 Form
 *
 */
class WarehouseTempletBillForm extends WarehouseTempletBill
{
    public $batch_sn;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
         $rules = [
             [['batch_sn'], 'string', 'max'=>30],
             [['batch_sn'], 'filter', 'filter' => 'trim'],
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
            'batch_sn' => '批次号',
        ]);
    }

   
}
