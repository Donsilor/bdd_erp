<?php

namespace addons\Purchase\common\forms;

use addons\Style\common\enums\AttrIdEnum;
use Yii;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;
use addons\Purchase\common\models\PurchaseReceipt;
use addons\Purchase\common\models\PurchaseStoneReceiptGoods;
/**
 * 采购收货单明细 Form
 *
 */
class PurchaseStoneReceiptGoodsForm extends PurchaseStoneReceiptGoods
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
     * 切工列表
     * @return array
     */
    public static function getCutMap()
    {
        return Yii::$app->attr->valueMap(AttrIdEnum::DIA_CUT);
    }
    /**
     * 对称列表
     * @return array
     */
    public static function getSymmetryMap()
    {
        return Yii::$app->attr->valueMap(AttrIdEnum::DIA_SYMMETRY);
    }
    /**
     * 抛光列表
     * @return array
     */
    public static function getPolishMap()
    {
        return Yii::$app->attr->valueMap(AttrIdEnum::DIA_POLISH);
    }
    /**
     * 荧光列表
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
     * {@inheritdoc}
     */
    public function getIds(){
        if($this->ids){
            return StringHelper::explode($this->ids);
        }
        return [];
    }
}
