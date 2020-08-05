<?php

namespace addons\Warehouse\common\forms;

use Yii;
use addons\Warehouse\common\models\WarehousePartsBillGoods;
use common\helpers\ArrayHelper;

/**
 * 配件单据明细 Form
 *
 */
class WarehousePartsBillLGoodsForm extends WarehousePartsBillGoods
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
