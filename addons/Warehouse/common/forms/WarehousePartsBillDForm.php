<?php

namespace addons\Warehouse\common\forms;

use Yii;
use addons\Warehouse\common\models\WarehousePartsBill;
use common\helpers\ArrayHelper;

/**
 * 配件单据 Form
 *
 */
class WarehousePartsBillDForm extends WarehousePartsBill
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
