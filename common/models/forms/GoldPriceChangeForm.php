<?php

namespace common\models\forms;

use Yii;
use common\helpers\ArrayHelper;
use common\models\common\GoldPrice;

/**
 * 会员管理 Form
 */
class GoldPriceChangeForm extends GoldPrice
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
                [['price'], 'required']
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
             'price'=>'当前金价(元/克)'   
        ]);
    }
    
}
