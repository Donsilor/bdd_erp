<?php

namespace addons\Warehouse\common\forms;

use Yii;
use addons\Warehouse\common\models\WarehouseGoldBillGoods;
use common\helpers\ArrayHelper;

/**
 * 单据明细 Form
 *
 */
class WarehouseGoldBillOGoodsForm extends WarehouseGoldBillGoods
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
         $rules = [
             [['out_type','gold_weight'], 'required'],
             [['gold_sn'],'unique'],
             ['gold_weight','compare','compareValue' => 0, 'operator' => '>'],
             //[['gold_sn','bill_type'],'unique','targetAttribute' => ['gold_sn', 'bill_type'],'comboNotUnique'=>'已被存在'],
             //[['gold_sn'],'unique', 'targetClass' => 'addons\Warehouse\common\models\WarehouseGold', 'message' => '库存已经存在.']
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
            'gold_sn' => '金料编号/批次号',
            'gold_weight' => '出库金重(g)',
        ]);
    }

}
