<?php

namespace addons\Warehouse\common\forms;

use addons\Sales\common\models\SaleChannel;
use common\models\backend\Member;
use Yii;
use addons\Warehouse\common\models\WarehouseGoldBill;
use common\helpers\ArrayHelper;

/**
 * 金料单据 Form
 *
 */
class WarehouseGoldBillOForm extends WarehouseGoldBill
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
         $rules = [
             [['out_type','channel_id'], 'required'],
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
    /**
     * 创建人
     * @return \yii\db\ActiveQuery
     */
    public function getReceiv()
    {
        return $this->hasOne(Member::class, ['id'=>'receiv_id'])->alias('receiv');
    }

    /**
     * 入库仓库 一对一
     * @return \yii\db\ActiveQuery
     */
    public function getChannel()
    {
        return $this->hasOne(SaleChannel::class, ['id'=>'channel_id'])->alias('channel');
    }
   
}
