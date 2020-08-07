<?php

namespace addons\Sales\common\forms;

use addons\Sales\common\models\OrderGoods;
use Yii;
use common\helpers\ArrayHelper;

/**
 * 订单 Form
 */
class OrderGiftForm extends OrderGoods
{

    //审批流程
    public $gift_id;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
                [['gift_id'],'required'],
        ];
        return ArrayHelper::merge(parent::rules(),$rules);
    }    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        //合并
        return ArrayHelper::merge(parent::attributeLabels() , [
                'gift_id' => '赠品',
                'goods_sn' => '批次号'
        ]);
    }

    
}
