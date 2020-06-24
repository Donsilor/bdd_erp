<?php

namespace addons\Warehouse\common\forms;

use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Warehouse\common\models\WarehouseBillGoodsA;
use addons\Warehouse\common\models\WarehouseGoods;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;


/**
 * 调整单明细 Form
 *
 */
class WarehouseBillAGoodsForm extends WarehouseBillGoodsA
{
    public $goods_ids;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['goods_ids'], 'required']
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
            'goods_ids' => '货号'
        ]);
    }


}
