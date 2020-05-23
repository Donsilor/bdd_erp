<?php

namespace addons\Warehouse\common\forms;

use addons\Warehouse\common\models\WarehouseBill;
use common\helpers\StringHelper;
/**
 * 盘点  Form
 *
 */
class WarehouseBillWForm extends WarehouseBill
{
    public $goods_ids;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
                [['to_warehouse_id','goods_ids'], 'required'],
        ];
        return array_merge(parent::rules() , $rules);
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        //合并
        return parent::attributeLabels() + [
                'goods_ids'=>'货号'
        ];
    }
    /**
     * 字符串转换成数组
     */
    public function getGoodsIds()
    {
        return StringHelper::explode($this->goods_ids);
    }
    
    
}
