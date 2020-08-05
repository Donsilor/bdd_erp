<?php

namespace addons\Purchase\common\forms;

use Yii;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;
use addons\Purchase\common\models\PurchasePartsReceiptGoods;
use addons\Style\common\enums\AttrIdEnum;

/**
 * 采购收货单明细 Form
 *
 */
class PurchasePartsReceiptGoodsForm extends PurchasePartsReceiptGoods
{
    public $ids;
    public $remark;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['remark'], 'string', 'max'=>255],
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
            'remark'=>'备注',
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
     * 配件类型
     * @return array
     */
    public function getPartsTypeMap()
    {
        return Yii::$app->attr->valueMap(AttrIdEnum::MAT_PARTS_TYPE);
    }
    /**
     * 颜色
     * @return array
     */
    public function getColorMap()
    {
        return Yii::$app->attr->valueMap(AttrIdEnum::MATERIAL_COLOR);
    }
    /**
     * 形状
     * @return array
     */
    public function getShapeMap()
    {
        return Yii::$app->attr->valueMap(AttrIdEnum::MAT_PARTS_SHAPE);
    }
    /**
     * 链类型
     * @return array
     */
    public function getChainTypeMap()
    {
        return Yii::$app->attr->valueMap(AttrIdEnum::CHAIN_TYPE);
    }
    /**
     * 扣环
     * @return array
     */
    public function getCrampRingMap()
    {
        return Yii::$app->attr->valueMap(AttrIdEnum::CHAIN_BUCKLE);
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
