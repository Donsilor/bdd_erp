<?php

namespace addons\Warehouse\common\forms;

use Yii;
use addons\Warehouse\common\models\WarehouseStoneBillGoods;
use addons\Style\common\enums\AttrIdEnum;
use common\helpers\ArrayHelper;

/**
 * 石包单据明细 Form
 *
 */
class WarehouseStoneBillCkGoodsForm extends WarehouseStoneBillGoods
{

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            ['stone_weight','compare','compareValue' => 0, 'operator' => '>'],
            ['stone_price','compare','compareValue' => 0, 'operator' => '>'],
            ['stone_num','compare','compareValue' => 0, 'operator' => '>'],
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
        ]);
    }






   
}
