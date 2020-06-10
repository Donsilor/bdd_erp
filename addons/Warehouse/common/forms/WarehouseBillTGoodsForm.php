<?php

namespace addons\Warehouse\common\forms;


use common\helpers\ArrayHelper;
use addons\Warehouse\common\models\WarehouseBillGoodsT;

/**
 * 其他收货单明细 Form
 *
 */
class WarehouseBillTGoodsForm extends WarehouseBillGoodsT
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['style_sn', 'goods_num'], 'required']
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
