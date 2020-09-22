<?php

namespace addons\Gdzb\common\forms;

use addons\Gdzb\common\models\OrderGoods;
use Yii;
use common\helpers\ArrayHelper;

/**
 * 订单 Form
 */
class OrderGoodsForm extends OrderGoods
{

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['goods_name','style_cate_id','product_type_id','goods_price','cost_price'], 'required'],
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



}
