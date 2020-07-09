<?php

namespace addons\Warehouse\common\forms;

use addons\Warehouse\common\models\WarehouseBill;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;

/**
 * 单据列表 Form
 *
 */
class WarehouseBillForm extends WarehouseBill
{
    public $goods_id;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
         $rules = [
             [['goods_id'], 'string', 'max'=>30],
             [['goods_id'], 'filter', 'filter' => 'trim'],
         ];
         return array_merge(parent::rules() , $rules);
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        //合并
        return ArrayHelper::merge(parent::attributeLabels() , [
            'goods_id' => '货号',
        ]);
    }
}
