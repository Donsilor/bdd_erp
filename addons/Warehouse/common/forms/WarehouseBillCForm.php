<?php

namespace addons\Warehouse\common\forms;

use addons\Warehouse\common\models\WarehouseBill;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;

/**
 * 其他出库单 Form
 *
 */
class WarehouseBillCForm extends WarehouseBill
{
    public $ids;
    public $goods_ids;
    public $returned_time;
    public $goods_remark;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
         $rules = [
            [['delivery_type', 'order_sn'], 'required']
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
            'order_sn'=>'参考编号/订单号',
            'goods_ids'=>'货号',
            'creator_id' => '制单人',
            'created_at' => '制单时间',
            'returned_time' => '还货日期',
            'goods_remark' => '质检备注',
        ]);
    }
    /**
     * {@inheritdoc}
     */
    public function getIds(){
        if($this->ids){
            return StringHelper::explode($this->ids);
        }
        return [];
    }
    /**
     * 批量获取货号
     */
    public function getGoodsIds()
    {
        return StringHelper::explodeIds($this->goods_ids);
    }
}
