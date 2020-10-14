<?php

namespace addons\Warehouse\common\forms;

use addons\Style\common\enums\AttrIdEnum;
use common\helpers\ArrayHelper;
use addons\Warehouse\common\models\WarehouseBillGoods;
use common\helpers\StringHelper;

/**
 * 其它出库单明细 Form
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

    /**
     * 入库方式
     * @return array
     */
    public function getPutInTypeMap()
    {
        return \addons\Warehouse\common\enums\PutInTypeEnum::getMap() ?? [];
    }

    /**
     * 入库仓库
     * @return array
     */
    public function getWarehouseMap()
    {
        return \Yii::$app->warehouseService->warehouse::getDropDown() ?? [];
    }

    /**
     * 材质列表
     * @return array
     */
    public function getMaterialTypeMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::MATERIAL_TYPE) ?? [];
    }
    /**
     * 材质颜色列表
     * @return array
     */
    public function getMaterialColorMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::MATERIAL_COLOR) ?? [];
    }
}
