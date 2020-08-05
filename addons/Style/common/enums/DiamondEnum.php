<?php
/**
 * Created by PhpStorm.
 * User: BDD
 * Date: 2019/11/25
 * Time: 14:07
 */
namespace addons\Style\common\enums;

class DiamondEnum extends  BaseEnum
{
    const CLARITY = 2;//净度ID
    const CUT = 4;//切工ID
    const CARAT = 5;//主石重量ID
    const SHAPE = 6;//钻石形状 ID
    const COLOR = 7;//颜色
    const FLUORESCENCE = 8;//荧光
    const FINENESS = 10;//成色
    const GRAM_WEIGHT = 11;//克重
    const PRODUC_CUSTOMIZATION = 12;//商品定制
    const SALE_SERVICES = 25;//售后服务
    const FOR_PEOPLE = 26;//适用人群
    const USE = 27;//用途
    const POLISH = 28;//光泽（抛光）
    const SYMMETRY = 29;//对称
    const APPRAISAL_AGENCY = 30;//鉴定机构
    const CERTIFICATE_NO = 31;//证书编号
    const CUTTING_DEPTH = 32;//切割深度（%）
    const STONE_SURFACE = 33;//石面（%）
    const LENGTH = 34;//长度（mm）
    const WIDTH = 35;//宽度（mm）
    const ASPECT_RATIO = 36;//长宽比（%）
    const STONE_FLOOR = 37;//石底层
    const SIZE = 38;//尺寸
    const RING_STYLE = 39;//对戒款式
    const ENGAGEMENT_STYLE = 40;//订婚戒指款式
    const HEIGHT = 41;//高度（mm）
    const BUCKLE = 42;//扣环
    const CHAIN_TYPE = 43;//链类型
    const SIDE_STONE_WEIGHT = 44;//副石重量(ct)
    const SIDE_STONE_QUANTITY = 45;//副石数量(个)
    const SIDE_STONE_COLOR = 46;//副石颜色
    const SIDE_STONE_CLARITY = 47;//副石净度
    const CERT_TYPE = 48;//钻石证书类型ID
    
    //证书类型
    public static function getCertTypeList()
    {
        return \Yii::$app->services->goodsAttribute->getValuesByAttrId(AttrIdEnum::CERT_TYPE);
    }
    //钻石颜色   
    //const COLOR = 'color';
    public static function getColorList()
    {
        return \Yii::$app->services->goodsAttribute->getValuesByAttrId(AttrIdEnum::COLOR);
    }

    //钻石净度
    public static function getClarityList()
    {
        return \Yii::$app->services->goodsAttribute->getValuesByAttrId(AttrIdEnum::CLARITY);
    }
    //钻石切工
    //const CUT = 'cut';
    public static function getCutList()
    {
        return \Yii::$app->services->goodsAttribute->getValuesByAttrId(AttrIdEnum::CUT);
    }
    //钻石形状
    //const SHAPE = 'shape';
    public static function getShapeList(){
        return \Yii::$app->services->goodsAttribute->getValuesByAttrId(AttrIdEnum::SHAPE);
    }
    //对称
    public static function getSymmetryList(){
        return \Yii::$app->services->goodsAttribute->getValuesByAttrId(AttrIdEnum::SYMMETRY);
    }
    //抛光
    public static function getPolishList(){
        return \Yii::$app->services->goodsAttribute->getValuesByAttrId(AttrIdEnum::POLISH);
    }

    //荧光
    public static function getFluorescenceList(){
        return \Yii::$app->services->goodsAttribute->getValuesByAttrId(AttrIdEnum::FLUORESCENCE);
    }

    //切割深度
    public static function getCuttingDepthList(){
        return \Yii::$app->services->goodsAttribute->getValuesByAttrId(AttrIdEnum::CUTTING_DEPTH);
    }

    //石底层
    public static function getStoneFloorList(){
        return \Yii::$app->services->goodsAttribute->getValuesByAttrId(AttrIdEnum::STONE_FLOOR);
    }

    //石面
    public static function getStoneSurfaceList(){
        return \Yii::$app->services->goodsAttribute->getValuesByAttrId(AttrIdEnum::STONE_SURFACE);
    }

    //长宽比
    public static function getAspectRatioList(){
        return \Yii::$app->services->goodsAttribute->getValuesByAttrId(AttrIdEnum::ASPECT_RATIO);
    }

    //售后服务
    public static function getSaleServicesList(){
        return \Yii::$app->services->goodsAttribute->getValuesByAttrId(AttrIdEnum::SALE_SERVICES);
    }



    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                
        ];
    }
}








