<?php

namespace addons\Warehouse\common\forms;

use common\helpers\ArrayHelper;
use addons\Warehouse\common\models\WarehousePartsBill;

/**
 * 盘点  Form
 *
 */
class WarehousePartsBillWForm extends WarehousePartsBill
{
    public $parts_sn;
    public $parts_type;
    public $parts_num;
    public $parts_weight;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['parts_type'], 'required'],
            [['parts_sn', 'parts_num', 'parts_weight'], 'filter', 'filter' => 'trim'],
            [['parts_sn'], 'string', 'max'=>30],
            [['parts_num'], 'integer'],
            [['parts_weight'], 'number'],
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
            'parts_type' => '盘点配件',
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
