<?php

namespace addons\Warehouse\common\forms;

use Yii;
use addons\Warehouse\common\models\WarehouseStoneBillGoods;
use common\helpers\ArrayHelper;

/**
 * 石包单据明细 Form
 *
 */
class WarehouseStoneBillSsGoodsForm extends WarehouseStoneBillGoods
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
         $rules = [
             [['stone_name', 'stone_num', 'stone_weight'], 'required'],
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
