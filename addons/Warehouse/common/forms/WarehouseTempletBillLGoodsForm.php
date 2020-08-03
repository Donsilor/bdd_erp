<?php

namespace addons\Warehouse\common\forms;

use Yii;
use addons\Warehouse\common\models\WarehouseTempletBillGoods;
use common\helpers\ArrayHelper;

/**
 * 样板单据明细 Form
 *
 */
class WarehouseTempletBillLGoodsForm extends WarehouseTempletBillGoods
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
