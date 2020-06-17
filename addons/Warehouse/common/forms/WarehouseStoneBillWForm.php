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
    public $stone_weight;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
                [['to_warehouse_id'], 'required'],
                [['stone_type'], 'integer'],
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
