<?php

namespace addons\Purchase\common\forms;


use addons\Purchase\common\models\PurchaseApplyGoods;
use common\helpers\ArrayHelper;

/**
 * 采购申请单商品部审核 Form
 *
 */
class PurchaseApplyGoodsConfimForm extends PurchaseApplyGoods
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['supplier_id'], 'required'],
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
        ]);
    }
}
