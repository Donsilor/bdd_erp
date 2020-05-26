<?php

namespace addons\Warehouse\common\forms;

use addons\Warehouse\common\models\WarehouseBill;
use common\helpers\StringHelper;
use common\helpers\ArrayHelper;
use addons\Warehouse\common\models\Warehouse;
use addons\Warehouse\common\models\WarehouseBillW;
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
                ['goods_ids','string']
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
     * 盘点单关联表
     * @return \yii\db\ActiveQuery
     */
    public function getBillW()
    {        
        return $this->hasOne(WarehouseBillW::class, ['bill_id'=>'id'])->alias('billW');
    }
    /**
     * 字符串转换成数组
     */
    public function getGoodsIds()
    {
        if($this->goods_ids == '') {
            throw new \Exception("货号不能为空");
        }
        return StringHelper::explodeIds($this->goods_ids);
    }
    /**
     * 盘点仓库
     * @return \yii\db\ActiveQuery
     */
    public function getWarehouse()
    {
        return $this->hasOne(Warehouse::class, ['id'=>'to_warehouse_id'])->alias('warehouse');
    }
    
    /**
     * @param bool $insert
     * @return bool
     * @throws \yii\base\Exception
     */
    public function beforeSave($insert)
    {           
        return parent::beforeSave($insert);
    }
    
}
