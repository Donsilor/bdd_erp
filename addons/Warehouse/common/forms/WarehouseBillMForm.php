<?php

namespace addons\Warehouse\common\forms;

use addons\Warehouse\common\models\WarehouseBill;
use common\helpers\ArrayHelper;

/**
 * 款式编辑-款式属性 Form
 *
 */
class WarehouseBillMForm extends WarehouseBill
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {      
         $rules = [
            [['to_warehouse_id'], 'required']
         ];
         return ArrayHelper::merge(parent::rules() , $rules);
    }

    public function attributeLabels()
    {
        //合并
        return ArrayHelper::merge(parent::attributeLabels() , [
            'goods_num'=>'数量',
            'total_cost'=>'总金额',

        ]);
    }

   
}
