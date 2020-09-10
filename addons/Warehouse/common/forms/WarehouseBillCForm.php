<?php

namespace addons\Warehouse\common\forms;

use addons\Warehouse\common\models\WarehouseBill;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;

/**
 * 其它出库单 Form
 *
 */
class WarehouseBillCForm extends WarehouseBill
{
    public $ids;
    public $goods_ids;
    public $file;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
         $rules = [
            [['delivery_type','channel_id'], 'required']                
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
            'channel_id'=>'出库渠道',
            'total_cost' =>'出库总成本',
            'salesman_id'=>'销售人/接收人',
            'goods_ids'=>'货号',            
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
