<?php

namespace addons\Purchase\common\forms;

use common\helpers\StringHelper;
use Yii;

use common\enums\ConfirmEnum;
use addons\Purchase\common\models\PurchaseStoneGoods;
use addons\Style\common\enums\AttrIdEnum;

/**
 * 金料商品 Form
 *
 * @property string $attr_require 必填属性
 * @property string $attr_custom 选填属性
 */
class PurchaseStoneGoodsForm extends PurchaseStoneGoods
{
    public $ids;
    public $put_in_type;
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
     * 石料采购商品申请编辑-创建
     */
    public function createApply()
    {
        //主要信息
        $fields = array('goods_name','cost_price','stone_num','stone_color','stone_clarity','remark');
        $apply_info = array();
        foreach ($fields as $field) {
            $apply_info[] = array(
                    'code'=>$field,
                    'value'=>$this->$field,
                    'label'=>$this->getAttributeLabel($field),
                    'group'=>'base',
            );
        }        
        $this->is_apply   = ConfirmEnum::YES;
        $this->apply_info = json_encode($apply_info);
        if(false === $this->save(true,['is_apply','apply_info','updated_at'])) {
            throw new \Exception("保存失败",500);
        }
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

