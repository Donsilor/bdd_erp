<?php

namespace addons\Warehouse\common\forms;

use common\helpers\ArrayHelper;
use addons\Warehouse\common\models\WarehouseStoneBillGoodsW;
use common\helpers\StringHelper;

/**
 * 盘点  Form
 *
 */
class WarehouseStoneBillGoodsWForm extends WarehouseStoneBillGoodsW
{
    public $ids;
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
    /**
     * {@inheritdoc}
     */
    public function getIds(){
        if($this->ids){
            return StringHelper::explode($this->ids);
        }
        return [];
    }
}
