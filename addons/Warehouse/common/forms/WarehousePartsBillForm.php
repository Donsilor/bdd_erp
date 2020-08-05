<?php

namespace addons\Warehouse\common\forms;

use Yii;
use addons\Warehouse\common\models\WarehousePartsBill;
use common\helpers\ArrayHelper;

/**
 * 单据列表 Form
 *
 */
class WarehousePartsBillForm extends WarehousePartsBill
{
    public $parts_sn;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
         $rules = [
             [['parts_sn'], 'string', 'max'=>30],
             [['parts_sn'], 'filter', 'filter' => 'trim'],
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
            'parts_sn' => '批次号',
        ]);
    }

   
}
