<?php

namespace addons\Warehouse\common\forms;

use Yii;
use addons\Warehouse\common\models\WarehouseTempletBillGoods;
use common\helpers\ArrayHelper;

/**
 * 样板单明细 Form
 *
 */
class WarehouseTempletBillCGoodsForm extends WarehouseTempletBillGoods
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
         $rules = [
             [['batch_sn'], 'required'],
             [['batch_sn'], 'filter', 'filter' => 'trim'],
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
