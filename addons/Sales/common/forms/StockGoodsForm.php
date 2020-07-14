<?php

namespace addons\Sales\common\forms;

use addons\Sales\common\models\OrderGoods;
use addons\Sales\common\models\OrderGoodsAttribute;
use addons\Style\common\enums\AttrIdEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\models\WarehouseGoods;
use common\helpers\ArrayHelper;

/**
 * 订单 Form
 */
class StockGoodsForm extends OrderGoods
{

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['goods_id'],'required'],
        ];
        return ArrayHelper::merge(parent::rules() , $rules);
    }




}
