<?php

namespace addons\Warehouse\common\forms;


use common\helpers\ArrayHelper;
use addons\Warehouse\common\models\WarehouseBillGoodsT;
use common\helpers\StringHelper;

/**
 * 其他收货单明细 Form
 *
 */
class WarehouseBillTGoodsForm extends WarehouseBillGoodsT
{
    public $ids;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['style_sn', 'goods_num'], 'required']
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
