<?php

namespace addons\Warehouse\common\forms;

use addons\Warehouse\common\models\WarehouseBill;
use common\helpers\ArrayHelper;

/**
 * 款式编辑-款式属性 Form
 *
 */
class WarehouseBillAForm extends WarehouseBill
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {      
         $rules = [
            [['from_warehouse_id'], 'required']
         ];
         return ArrayHelper::merge(parent::rules() , $rules);
    }

    public function attributeLabels()
    {
        //合并
        return ArrayHelper::merge(parent::attributeLabels() , [
            'from_warehouse_id'=> '调整仓库',
            'goods_num'=>'数量',
            'total_cost'=>'总金额',

        ]);
    }

   
}
