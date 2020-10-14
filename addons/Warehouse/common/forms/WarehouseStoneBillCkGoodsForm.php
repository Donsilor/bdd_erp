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
            [['cost_price'], 'parseCostPriceScope'],
            [['carat'], 'parseCaratScope'],
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
            'stone_num' => '出库粒数',
            'stone_weight' => '出库石重（ct)',
            'cost_price' => '出库石料总额',
        ]);
    }
    public function parseCostPriceScope(){
        $stone_price = $this->stone_price ?? 0;
        $stone_weight = $this->stone_weight ?? 0;
        $this->cost_price = $stone_price * $stone_weight;
        return $this->cost_price;
    }

    public function parseCaratScope(){
        $stone_weight = $this->stone_weight ?? 0;
        $stone_num = $this->stone_num ?? 1;
        $this->carat = round($stone_weight / $stone_num,3);
        return $this->carat;
    }





   
}
