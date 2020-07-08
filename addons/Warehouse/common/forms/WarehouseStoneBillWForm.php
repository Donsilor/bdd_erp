<?php

namespace addons\Warehouse\common\forms;

use common\helpers\ArrayHelper;
use addons\Warehouse\common\models\WarehouseStoneBill;
/**
 * 盘点  Form
 *
 */
class WarehouseStoneBillWForm extends WarehouseStoneBill
{
    public $stone_sn;
    public $stone_type;
    public $stone_num;
    public $stone_weight;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['stone_type'], 'required'],
            [['stone_sn', 'stone_num', 'stone_weight'], 'filter', 'filter' => 'trim'],
            [['stone_sn'], 'string', 'max'=>30],
            [['stone_num'], 'integer'],
            [['stone_weight'], 'number'],
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
            'stone_type' => '盘点石料',
        ]);
    }
    /**
     * 获取仓库下拉列表
     * @return unknown
     */
    public function getWarehouseDropdown()
    {
        if($this->id) {
            return \Yii::$app->warehouseService->warehouse->getDropDown();
        }else{
            return \Yii::$app->warehouseService->warehouse->getDropDownForUnlock();
        }
    }
}
