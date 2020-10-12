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
class WarehouseStoneBillRkGoodsForm extends WarehouseStoneBillGoods
{

    public $file;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            ['stone_weight','compare','compareValue' => 0, 'operator' => '>'],
            ['stone_price','compare','compareValue' => 0, 'operator' => '>'],
            ['stone_num','compare','compareValue' => 0, 'operator' => '>'],
//            [['stone_sn','bill_type'],'unique','targetAttribute' => ['stone_sn', 'bill_type'],'comboNotUnique'=>'石料编号不能重复'],
//            [['stone_sn'],'unique', 'targetClass' => 'addons\Warehouse\common\models\WarehouseGold', 'message' => '库存中石料编号已经存在.']
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
