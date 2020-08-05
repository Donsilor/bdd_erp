<?php

namespace addons\Warehouse\common\forms;

use common\helpers\ArrayHelper;
use addons\Warehouse\common\models\WarehouseGoldBill;
/**
 * 盘点  Form
 *
 */
class WarehouseGoldBillWForm extends WarehouseGoldBill
{
    public $gold_sn;
    public $gold_type;
    public $gold_weight;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['gold_type'], 'required'],
            [['gold_sn', 'gold_weight'], 'filter', 'filter' => 'trim'],
            [['gold_sn'], 'string', 'max'=>30],
            [['gold_weight'], 'number'],
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
            'gold_type' => '盘点材质',
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
