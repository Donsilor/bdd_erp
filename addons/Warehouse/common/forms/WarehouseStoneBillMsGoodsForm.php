<?php

namespace addons\Warehouse\common\forms;

use Yii;
use addons\Warehouse\common\models\WarehouseStoneBillGoods;
use addons\Style\common\enums\AttrIdEnum;
use common\helpers\ArrayHelper;

/**
 * 石包单据明细 Form
 *
 */
class WarehouseStoneBillMsGoodsForm extends WarehouseStoneBillGoods
{
    /**
     * 石料类型列表
     * @return array
     */
    public static function getStoneTypeMap()
    {
        return Yii::$app->attr->valueMap(AttrIdEnum::MAT_STONE_TYPE);
    }
    /**
     * 石料形状列表
     * @return array
     */
    public static function getShapeMap()
    {
        return Yii::$app->attr->valueMap(AttrIdEnum::DIA_SHAPE);
    }
    /**
     * 石料颜色列表
     * @return array
     */
    public static function getColorMap()
    {
        return Yii::$app->attr->valueMap(AttrIdEnum::DIA_COLOR);
    }
    /**
     * 石料净度列表
     * @return array
     */
    public static function getClarityMap()
    {
        return Yii::$app->attr->valueMap(AttrIdEnum::DIA_CLARITY);
    }
    /**
     * 石料切工列表
     * @return array
     */
    public static function getCutMap()
    {
        return Yii::$app->attr->valueMap(AttrIdEnum::DIA_CUT);
    }
    /**
     * 石料对称列表
     * @return array
     */
    public static function getSymmetryMap()
    {
        return Yii::$app->attr->valueMap(AttrIdEnum::DIA_SYMMETRY);
    }
    /**
     * 石料抛光列表
     * @return array
     */
    public static function getPolishMap()
    {
        return Yii::$app->attr->valueMap(AttrIdEnum::DIA_POLISH);
    }
    /**
     * 石料荧光列表
     * @return array
     */
    public static function getFluorescenceMap()
    {
        return Yii::$app->attr->valueMap(AttrIdEnum::DIA_FLUORESCENCE);
    }
    /**
     * 证书类型列表
     * @return array
     */
    public static function getCertTypeMap()
    {
        return Yii::$app->attr->valueMap(AttrIdEnum::DIA_CERT_TYPE);
    }
    /**
     * 石料色彩列表
     * @return array
     */
    public static function getColourMap()
    {
        return Yii::$app->attr->valueMap(AttrIdEnum::DIA_COLOUR);
    }
    /**
     * 石料款号列表
     * @return array
     */
    public static function getStyleSnMap()
    {
        return Yii::$app->styleService->stone->getDropDown();
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
         $rules = [

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

   
}
