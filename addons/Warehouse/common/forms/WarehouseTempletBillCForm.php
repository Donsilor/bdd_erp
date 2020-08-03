<?php

namespace addons\Warehouse\common\forms;

use Yii;
use addons\Warehouse\common\models\WarehouseTempletBill;
use common\helpers\ArrayHelper;

/**
 * 样板单据 Form
 *
 */
class WarehouseTempletBillCForm extends WarehouseTempletBill
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
         $rules = [
             [['created_at'], 'integer'],
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
