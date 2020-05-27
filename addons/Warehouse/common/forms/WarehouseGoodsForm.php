<?php

namespace addons\Warehouse\common\forms;

use addons\Warehouse\common\models\WarehouseGoods;
use common\helpers\ArrayHelper;

/**
 * 维修退货单 Form
 *
 */
class WarehouseGoodsForm extends WarehouseGoods
{
    public $goods_ids;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {      
         $rules = [
            [[], 'required']
         ];
         return array_merge(parent::rules() , $rules);
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

    public function createApply(){
        $attr_fields = $this->activeAttributes();

        foreach ($attr_fields as $attr){
            echo  $attr."<br/>";
        }
    }

}
