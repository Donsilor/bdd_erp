<?php

namespace addons\Warehouse\common\forms;

use common\helpers\ArrayHelper;
use addons\Warehouse\common\models\WarehouseBillGoods;
use common\helpers\StringHelper;

/**
 * 其他出库单明细 Form
 *
 */
class WarehouseBillCGoodsForm extends WarehouseBillGoods
{
    public $ids;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [

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
            'from_warehouse_id'=>'出库仓库',
            'to_warehouse_id'=>'入库仓库'
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
