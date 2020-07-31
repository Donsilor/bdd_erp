<?php

namespace addons\Purchase\common\forms;

use Yii;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;
use addons\Purchase\common\models\PurchaseGiftReceiptGoods;
use addons\Style\common\enums\AttrIdEnum;

/**
 * 采购收货单明细 Form
 *
 */
class PurchaseGiftReceiptGoodsForm extends PurchaseGiftReceiptGoods
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
            'id'=>'流水号',
        ]);
    }
    /**
     * 材质列表
     * @return array
     */
    public function getMaterialTypeMap()
    {
        return Yii::$app->attr->valueMap(AttrIdEnum::MATERIAL_TYPE);
    }
    /**
     * 材质颜色列表
     * @return array
     */
    public function getMaterialColorMap()
    {
        return Yii::$app->attr->valueMap(AttrIdEnum::MATERIAL_COLOR);
    }
    /**
     * 主石类型列表
     * @return array
     */
    public function getMainStoneTypeMap()
    {
        return Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_TYPE);
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
