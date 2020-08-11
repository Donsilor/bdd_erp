<?php

namespace addons\Warehouse\common\forms;

use common\helpers\ArrayHelper;
use addons\Warehouse\common\models\WarehouseBillGoodsL;
use common\helpers\StringHelper;

/**
 * 其他收货单明细 Form
 *
 */
class WarehouseBillTGoodsForm extends WarehouseBillGoodsL
{
    public $ids;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['goods_sn', 'is_wholesale', 'auto_goods_id', 'goods_num'], 'required']
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
            'is_wholesale' => '是否批发(批发入库时出库销售不可拆分)'
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
