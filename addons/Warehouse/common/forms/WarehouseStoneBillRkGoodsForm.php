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
            [['stone_sn'],'default','value' => NULL],
            [['cost_price'], 'parseCostPriceScope'],
            [['carat'], 'parseCaratScope'],
            [['stone_sn','bill_type'],'unique','targetAttribute' => ['stone_sn', 'bill_type'],'message'=>'石料编号不能重复','when' => function ($model) {
                return !empty($model->stone_sn);
            }],
            [['stone_sn'],'unique', 'targetClass' => 'addons\Warehouse\common\models\WarehouseStone', 'message' => '库存中石料编号已经存在.']
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
