<?php

namespace addons\Warehouse\common\forms;

use addons\Warehouse\common\models\WarehouseBill;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;

/**
 * 退货返厂单 Form
 *
 */
class WarehouseBillBForm extends WarehouseBill
{
    public $goods_ids;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {      
         $rules = [
            [['put_in_type', 'supplier_id'], 'required']
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
            'goods_ids'=>'货号'
        ]);
    }

    /**
     * 批量获取货号
     */
    public function getGoodsIds()
    {
        return StringHelper::explodeIds($this->goods_ids);
    }
}
