<?php

namespace addons\Warehouse\common\forms;

use addons\Warehouse\common\models\WarehouseStone;
use common\helpers\ArrayHelper;

/**
 * 石包 Form
 *
 */
class WarehouseStoneForm extends WarehouseStone
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
        //return ArrayHelper::merge(parent::attributeLabels() , [
        //]);
    }

   
}
