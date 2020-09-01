<?php

namespace addons\Purchase\common\forms;

use Yii;
use common\helpers\ArrayHelper;
use addons\Style\common\enums\AttrIdEnum;
use addons\Purchase\common\models\PurchaseReceiptGoods;
use common\helpers\StringHelper;

/**
 * 采购收货单明细 Form
 *
 */
class PurchaseReceiptGoodsForm extends PurchaseReceiptGoods
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
            'jintuo_type'=>'金托类型',
            'remark'=>'备注',
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
     * {@inheritdoc}
     */
    public function getGoodsView(){
        $label = $this->attributeLabels();
        $data = [];
        foreach ($this->toArray() as $k => $item) {
            $data[$label[$k]] = $item;
        }
        return $data;
    }

    /**
     * 根据款号获取属性值
     * @param string $style_sn
     * @param integer $attr_id
     * @return array
     */
    public function getAttrValueListByStyle($style_sn, $attr_id)
    {
        return \Yii::$app->styleService->styleAttribute->getAttrValueListByStyle($style_sn, $attr_id) ?? [];
    }

    /**
     * 款式分类列表
     * @return array
     */
    public function getCateMap()
    {
        return \Yii::$app->styleService->styleCate::getDropDown() ?? [];
    }

    /**
     * 产品线列表
     * @return array
     */
    public function getProductMap()
    {
        return \Yii::$app->styleService->productType::getDropDown() ?? [];
    }

    /**
     * 起版类型
     * @return array
     */
    public function getQibanTypeMap()
    {
        return \addons\Style\common\enums\QibanTypeEnum::getMap() ?? [];
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
     * 材质
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getMaterialTypeDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::MATERIAL_TYPE);
        } else {
            $data = $this->getMaterialTypeMap();
        }
        return $data ?? [];
    }

    /**
     * 材质颜色列表
     * @return array
     */
    public function getMaterialColorMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::MATERIAL_COLOR) ?? [];
    }

    /**
     * 材质颜色
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getMaterialColorDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::MATERIAL_COLOR);
        } else {
            $data = $this->getMaterialColorMap();
        }
        return $data ?? [];
    }

    /**
     * 港号列表
     * @return array
     */
    public function getPortNoMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::PORT_NO) ?? [];
    }

    /**
     * 港号
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getPortNoDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::PORT_NO);
        } else {
            $data = $this->getPortNoMap();
        }
        return $data ?? [];
    }

    /**
     * 美号列表
     * @return array
     */
    public function getFingerMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::FINGER) ?? [];
    }

    /**
     * 美号
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getFingerDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::FINGER);
        } else {
            $data = $this->getFingerMap();
        }
        return $data ?? [];
    }

    /**
     * 镶口列表
     * @return array
     */
    public function getXiangkouMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::XIANGKOU) ?? [];
    }

    /**
     * 镶口
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getXiangkouDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::XIANGKOU);
        } else {
            $data = $this->getXiangkouMap();
        }
        return $data ?? [];
    }

    /**
     * 链类型列表
     * @return array
     */
    public function getChainTypeMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::CHAIN_TYPE) ?? [];
    }

    /**
     * 链类型
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getChainTypeDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::CHAIN_TYPE);
        } else {
            $data = $this->getChainTypeMap();
        }
        return $data ?? [];
    }

    /**
     * 链扣环列表
     * @return array
     */
    public function getCrampRingMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::CHAIN_BUCKLE) ?? [];
    }

    /**
     * 链扣环
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getCrampRingDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::CHAIN_BUCKLE);
        } else {
            $data = $this->getCrampRingMap();
        }
        return $data ?? [];
    }

    /**
     * 爪头形状列表
     * @return array
     */
    public function getTalonHeadTypeMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::TALON_HEAD_TYPE) ?? [];
    }

    /**
     * 爪头形状
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getTalonHeadTypeDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::TALON_HEAD_TYPE);
        } else {
            $data = $this->getTalonHeadTypeMap();
        }
        return $data ?? [];
    }

    /**
     * 证书类型列表
     * @return array
     */
    public function getCertTypeMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::DIA_CERT_TYPE) ?? [];
    }

    /**
     * 证书类型
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getCertTypeDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::DIA_CERT_TYPE);
        } else {
            $data = $this->getCertTypeMap();
        }
        return $data ?? [];
    }

    /**
     * 钻石证书类型列表
     * @return array
     */
    public function getDiamondCertTypeMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::DIA_CERT_TYPE) ?? [];
    }

    /**
     * 钻石证书类型
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getDiamondCertTypeDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::DIA_CERT_TYPE);
        } else {
            $data = $this->getDiamondCertTypeMap();
        }
        return $data ?? [];
    }

    /**
     * 钻石颜色列表
     * @return array
     */
    public function getDiamondColorMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::DIA_COLOR) ?? [];
    }

    /**
     * 钻石颜色
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getDiamondColorDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::DIA_COLOR);
        } else {
            $data = $this->getDiamondColorMap();
        }
        return $data ?? [];
    }

    /**
     * 钻石形状列表
     * @return array
     */
    public function getDiamondShapeMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::DIA_SHAPE) ?? [];
    }

    /**
     * 钻石形状
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getDiamondShapeDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::DIA_SHAPE);
        } else {
            $data = $this->getDiamondShapeMap();
        }
        return $data ?? [];
    }

    /**
     * 钻石净度列表
     * @return array
     */
    public function getDiamondClarityMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::DIA_CLARITY) ?? [];
    }

    /**
     * 钻石净度
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getDiamondClarityDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::DIA_CLARITY);
        } else {
            $data = $this->getDiamondClarityMap();
        }
        return $data ?? [];
    }

    /**
     * 钻石切工列表
     * @return array
     */
    public function getDiamondCutMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::DIA_CUT) ?? [];
    }

    /**
     * 钻石切工
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getDiamondCutDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::DIA_CUT);
        } else {
            $data = $this->getDiamondCutMap();
        }
        return $data ?? [];
    }

    /**
     * 钻石抛光列表
     * @return array
     */
    public function getDiamondPolishMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::DIA_POLISH) ?? [];
    }

    /**
     * 钻石抛光
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getDiamondPolishDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::DIA_POLISH);
        } else {
            $data = $this->getDiamondPolishMap();
        }
        return $data ?? [];
    }

    /**
     * 钻石对称列表
     * @return array
     */
    public function getDiamondSymmetryMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::DIA_SYMMETRY) ?? [];
    }

    /**
     * 钻石对称
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getDiamondSymmetryDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::DIA_SYMMETRY);
        } else {
            $data = $this->getDiamondSymmetryMap();
        }
        return $data ?? [];
    }

    /**
     * 钻石荧光列表
     * @return array
     */
    public function getDiamondFluorescenceMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::DIA_FLUORESCENCE) ?? [];
    }

    /**
     * 钻石荧光
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getDiamondFluorescenceDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::DIA_FLUORESCENCE);
        } else {
            $data = $this->getDiamondFluorescenceMap();
        }
        return $data ?? [];
    }

    /**
     * 主石类型列表
     * @return array
     */
    public function getMainStoneTypeMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_TYPE) ?? [];
    }

    /**
     * 主石类型
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getMainStoneTypeDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::MAIN_STONE_TYPE);
        } else {
            $data = $this->getMainStoneTypeMap();
        }
        return $data ?? [];
    }

    /**
     * 主石颜色列表
     * @return array
     */
    public function getMainStoneColorMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_COLOR) ?? [];
    }

    /**
     * 主石颜色
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getMainStoneColorDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::MAIN_STONE_COLOR);
        } else {
            $data = $this->getMainStoneColorMap();
        }
        return $data ?? [];
    }

    /**
     * 主石形状列表
     * @return array
     */
    public function getMainStoneShapeMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_SHAPE) ?? [];
    }

    /**
     * 主石形状
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getMainStoneShapeDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::MAIN_STONE_SHAPE);
        } else {
            $data = $this->getMainStoneShapeMap();
        }
        return $data ?? [];
    }

    /**
     * 主石净度列表
     * @return array
     */
    public function getMainStoneClarityMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_CLARITY) ?? [];
    }

    /**
     * 主石净度
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getMainStoneClarityDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::MAIN_STONE_CLARITY);
        } else {
            $data = $this->getMainStoneClarityMap();
        }
        return $data ?? [];
    }

    /**
     * 主石切工列表
     * @return array
     */
    public function getMainStoneCutMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_CUT) ?? [];
    }

    /**
     * 主石切工
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getMainStoneCutDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::MAIN_STONE_CUT);
        } else {
            $data = $this->getMainStoneCutMap();
        }
        return $data ?? [];
    }

    /**
     * 主石色彩列表
     * @return array
     */
    public function getMainStoneColourMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_COLOUR) ?? [];
    }

    /**
     * 主石色彩
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getMainStoneColourDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::MAIN_STONE_COLOUR);
        } else {
            $data = $this->getMainStoneColourMap();
        }
        return $data ?? [];
    }

    /**
     * 副石1类型列表
     * @return array
     */
    public function getSecondStoneType1Map()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_TYPE) ?? [];
    }

    /**
     * 副石1类型
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getSecondStoneType1Drop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::SIDE_STONE1_TYPE);
        } else {
            $data = $this->getSecondStoneType1Map();
        }
        return $data ?? [];
    }

    /**
     * 副石1形状列表
     * @return array
     */
    public function getSecondStoneShape1Map()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_SHAPE) ?? [];
    }

    /**
     * 副石1形状
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getSecondStoneShape1Drop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::SIDE_STONE1_SHAPE);
        } else {
            $data = $this->getSecondStoneShape1Map();
        }
        return $data ?? [];
    }

    /**
     * 副石1颜色列表
     * @return array
     */
    public function getSecondStoneColor1Map()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_COLOR) ?? [];
    }

    /**
     * 副石1颜色
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getSecondStoneColor1Drop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::SIDE_STONE1_COLOR);
        } else {
            $data = $this->getSecondStoneColor1Map();
        }
        return $data ?? [];
    }

    /**
     * 副石1净度列表
     * @return array
     */
    public function getSecondStoneClarity1Map()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_CLARITY) ?? [];
    }

    /**
     * 副石1净度
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getSecondStoneClarity1Drop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::SIDE_STONE1_CLARITY);
        } else {
            $data = $this->getSecondStoneClarity1Map();
        }
        return $data ?? [];
    }

    /**
     * 副石1色彩列表
     * @return array
     */
    public function getSecondStoneColour1Map()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_COLOUR) ?? [];
    }

    /**
     * 副石1色彩
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getSecondStoneColour1Drop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::SIDE_STONE1_COLOUR);
        } else {
            $data = $this->getSecondStoneColour1Map();
        }
        return $data ?? [];
    }

    /**
     * 副石2类型列表
     * @return array
     */
    public function getSecondStoneType2Map()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE2_TYPE) ?? [];
    }

    /**
     * 副石2类型
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getSecondStoneType2Drop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::SIDE_STONE2_TYPE);
        } else {
            $data = $this->getSecondStoneType2Map();
        }
        return $data ?? [];
    }

    /**
     * 副石2形状列表
     * @return array
     */
    public function getSecondStoneShape2Map()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE2_SHAPE) ?? [];
    }

    /**
     * 副石2形状
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getSecondStoneShape2Drop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::SIDE_STONE2_SHAPE);
        } else {
            $data = $this->getSecondStoneShape2Map();
        }
        return $data ?? [];
    }

    /**
     * 副石2颜色列表
     * @return array
     */
    public function getSecondStoneColor2Map()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE2_COLOR) ?? [];
    }

    /**
     * 副石2颜色
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getSecondStoneColor2Drop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::SIDE_STONE2_COLOR);
        } else {
            $data = $this->getSecondStoneColor2Map();
        }
        return $data ?? [];
    }

    /**
     * 副石2净度列表
     * @return array
     */
    public function getSecondStoneClarity2Map()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE2_CLARITY) ?? [];
    }

    /**
     * 副石2净度
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getSecondStoneClarity2Drop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::SIDE_STONE2_CLARITY);
        } else {
            $data = $this->getSecondStoneClarity2Map();
        }
        return $data ?? [];
    }

    /**
     * 副石2色彩列表
     * @return array
     */
    public function getSecondStoneColour2Map()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE2_COLOUR) ?? [];
    }

    /**
     * 副石2色彩
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getSecondStoneColour2Drop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::SIDE_STONE2_COLOUR);
        } else {
            $data = $this->getSecondStoneColour2Map();
        }
        return $data ?? [];
    }

    /**
     * 副石3类型列表
     * @return array
     */
    public function getSecondStoneType3Map()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE3_TYPE) ?? [];
    }

    /**
     * 副石3类型
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getSecondStoneType3Drop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::SIDE_STONE3_TYPE);
        } else {
            $data = $this->getSecondStoneType3Map();
        }
        return $data ?? [];
    }

    /**
     * 配件类型列表
     * @return array
     */
    public function getPartsTypeMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::MAT_PARTS_TYPE) ?? [];
    }

    /**
     * 配件类型
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getPartsTypeDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::MAT_PARTS_TYPE);
        } else {
            $data = $this->getPartsTypeMap();
        }
        return $data ?? [];
    }

    /**
     * 配件材质列表
     * @return array
     */
    public function getPartsMaterialMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::MATERIAL_TYPE) ?? [];
    }

    /**
     * 配件材质
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getPartsMaterialDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::MATERIAL_TYPE);
        } else {
            $data = $this->getPartsMaterialMap();
        }
        return $data ?? [];
    }

    /**
     * 镶嵌工艺列表
     * @return array
     */
    public function getXiangqianCraftMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::XIANGQIAN_CRAFT) ?? [];
    }

    /**
     * 镶嵌工艺
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getXiangqianCraftDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::XIANGQIAN_CRAFT);
        } else {
            $data = $this->getXiangqianCraftMap();
        }
        return $data ?? [];
    }

    /**
     * 表面工艺列表
     * @return array
     */
    public function getFaceCraftMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::FACEWORK) ?? [];
    }

    /**
     * 表面工艺
     * @param PurchaseReceiptGoods $form
     * @return array
     */
    public function getFaceCraftDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::FACEWORK);
        } else {
            $data = $this->getFaceCraftMap();
        }
        return $data ?? [];
    }

    /**
     * 款式性别
     * @return array
     */
    public function getStyleSexMap()
    {
        return \addons\Style\common\enums\StyleSexEnum::getMap() ?? [];
    }

    /**
     * 金托类型
     * @return array
     */
    public function getJietuoTypeMap()
    {
        return \addons\Style\common\enums\JintuoTypeEnum::getMap() ?? [];
    }

    /**
     * 是否镶嵌
     * @return array
     */
    public function getIsInlayMap()
    {
        return \addons\Style\common\enums\InlayEnum::getMap() ?? [];
    }

    /**
     * 配件方式
     * @return array
     */
    public function getPeiLiaoWayMap()
    {
        return \addons\Warehouse\common\enums\PeiLiaoWayEnum::getMap() ?? [];
    }

    /**
     * 配件方式
     * @return array
     */
    public function getPeiJianWayMap()
    {
        return \addons\Warehouse\common\enums\PeiJianWayEnum::getMap() ?? [];
    }

    /**
     * 配石方式(类型)
     * @return array
     */
    public function getPeiShiWayMap()
    {
        return \addons\Warehouse\common\enums\PeiShiWayEnum::getMap() ?? [];
    }
}
