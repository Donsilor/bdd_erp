<?php

namespace addons\Warehouse\common\forms;

use Yii;
use addons\Warehouse\common\models\WarehouseGoldBillGoods;
use common\helpers\ArrayHelper;

/**
 * 单据明细 Form
 *
 */
class WarehouseGoldBillGoodsForm extends WarehouseGoldBillGoods
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
         $rules = [

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
