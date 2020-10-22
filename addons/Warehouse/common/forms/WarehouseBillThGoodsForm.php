<?php

namespace addons\Warehouse\common\forms;

use common\helpers\ArrayHelper;
use addons\Warehouse\common\models\WarehouseBillGoods;

/**
 * 销售退货单明细 Form
 *
 */
class WarehouseBillThGoodsForm extends WarehouseBillGoods
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
                
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
             'to_warehouse_id' =>'退货仓库',  
             'cost_price' =>'退货成本价/件',
             'cost_amount' =>'退货成本总额',
             'goods_num' =>'退货数量',
        ]);
    }    
}
