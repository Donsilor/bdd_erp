<?php

namespace addons\Warehouse\common\forms;

use common\helpers\ArrayHelper;
use addons\Warehouse\common\models\WarehouseBill;
use common\helpers\StringHelper;

/**
 * 借货单 Form
 *
 */
class WarehouseBillJForm extends WarehouseBill
{
    public $ids;
    public $lender_id;
    public $lend_status;
    public $restore_num;
    public $est_restore_time;
    public $returned_time;
    public $goods_remark;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
         $rules = [
             [['channel_id', 'lender_id', 'est_restore_time'], 'required'],
             [['lend_status'], 'integer'],
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
            'order_sn'=>'参考编号',
            'goods_num'=>'借货数量',
            'lender_id'=>'借货人',
            'lend_status'=>'借货状态',
            'channel_id'=>'借货渠道',
            'restore_num'=>'还货数量',
            'creator_id'=>'制单人',
            'created_at'=>'制单时间',
            'est_restore_time'=>'预计还货日期',
            'returned_time'=>'还货日期',
            'goods_remark'=>'质检备注',
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
}
