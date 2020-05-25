<?php

namespace addons\Warehouse\common\forms;

use addons\Warehouse\common\models\WarehouseBill;
use common\helpers\StringHelper;
use common\helpers\ArrayHelper;
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
                [['to_warehouse_id'], 'required'],
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
                'goods_ids'=>'货号',
                'to_warehouse_id'=>'盘点仓库'
        ]);
    }
    /**
     * 字符串转换成数组
     */
    public function getGoodsIds()
    {
        return StringHelper::explodeIds($this->goods_ids);
    }
    
    public function getWarehoseId()
    {
        return $this->to_warehouse_id;
    }
    
    
}
