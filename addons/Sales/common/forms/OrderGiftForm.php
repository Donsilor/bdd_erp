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

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [

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
                'goods_sn' => '批次号'
        ]);
    }

    
}
