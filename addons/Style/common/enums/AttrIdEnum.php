<?php

namespace addons\Style\common\enums;

/**
 * 属性ID枚举
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class AttrIdEnum 
{
    //金属信息
    const JINZHONG = 11;//金重
    const FINGER = 38;//美号（手寸）
    const PORT_NO = 78;//港号（手寸）
    const MATERIAL_TYPE = 10;//材质
    const MATERIAL_COLOR = 77;//材质颜色
    const MATERIAL = 10;//材质（成色）
    const XIANGKOU = 49;//镶口
    const INLAY_METHOD = 58;//镶嵌方式
    const CHAIN_LENGTH = 53;//链长
    const CHAIN_TYPE = 43;//链类型
    const CHAIN_BUCKLE = 42;//链扣环
    const HEIGHT = 41;//高度（mm）
    const FACEWORK = 57;//表面工艺
    
    //物料
    const MAT_STONE_TYPE = 40;//石料类型
    const MAT_GOLD_TYPE = 51;//金料类型
    const MAT_PARTS_TYPE = 93;//配件类型
    const MAT_PARTS_SHAPE = 94;//配件形状
    
    //钻石信息
    const DIA_CLARITY = 2;//钻石净度
    const DIA_CUT = 4;//钻石切工
    const DIA_CARAT = 59;//钻石大小
    const DIA_SHAPE = 6;//钻石形状 
    const DIA_COLOR = 7;//钻石颜色
    const DIA_FLUORESCENCE = 8;//荧光
    const DIA_CERT_NO = 31;//证书编号
    const DIA_CERT_TYPE = 48;//证书类型
    const DIA_COLOUR = 87;//钻石色彩
    const DIA_CUT_DEPTH = 32;//切割深度（%）
    const DIA_TABLE_LV = 33;//台宽比（%）
    const DIA_LENGTH = 34;//长度（mm）
    const DIA_WIDTH = 35;//宽度（mm）
    const DIA_ASPECT_RATIO = 36;//长宽比（%）
    const DIA_STONE_FLOOR = 37;//石底层
    const DIA_POLISH = 28;//抛光
    const DIA_SYMMETRY = 29;//对称
    const DIA_SPEC = 999;//规格(石头备注)

    //主石信息
    const MAIN_STONE_TYPE = 56;//主石类型
    const MAIN_STONE_WEIGHT = 59;//主石大小
    const MAIN_STONE_NUM = 65;//主石数量
    const MAIN_STONE_SHAPE = 6;//主石形状
    const MAIN_STONE_COLOR = 7;//主石颜色
    const MAIN_STONE_PRICE = 61;//主石单价
    const MAIN_STONE_SECAI = 87;//主石颜色
    const MAIN_STONE_CLARITY = 2;//主石净度
    const MAIN_STONE_CUT = 4;//主石切工
    const MAIN_STONE_SYMMETRY = 29;//主石对称
    const MAIN_STONE_POLISH = 28;//主石抛光
    const MAIN_STONE_FLUORESCENCE = 8;//主石荧光
    const MAIN_STONE_COLOUR = 87;//主石色彩
    
    //副石1信息
    const SIDE_STONE1_TYPE = 60;//副石1类型
    const SIDE_STONE1_SHAPE = 84;//副石1形状
    const SIDE_STONE1_COLOR = 46;//副石1颜色
    const SIDE_STONE1_SECAI = 88;//副石1色彩
    const SIDE_STONE1_PRICE = 95;//副石1单价
    const SIDE_STONE1_CLARITY = 47;//副石1净度
    const SIDE_STONE1_WEIGHT = 44;//副石1重量(ct)
    const SIDE_STONE1_NUM = 45;//副石1数量
    const SIDE_STONE1_CUT = 97;//副石1切工
    const SIDE_STONE1_SPEC = 999;//副石1规格
    const SIDE_STONE1_COLOUR = 88;//副石1色彩

    //副石2信息
    const SIDE_STONE2_TYPE = 64;//副石2类型
    const SIDE_STONE2_SHAPE = 85;//副石2形状
    const SIDE_STONE2_COLOR = 46;//副石2颜色
    const SIDE_STONE2_SECAI = 89;//副石2色彩
    const SIDE_STONE2_PRICE = 96;//副石2单价
    const SIDE_STONE2_CLARITY = 47;//副石2净度
    const SIDE_STONE2_WEIGHT = 63;//副石2重量(ct)
    const SIDE_STONE2_NUM = 62;//副石2数量
    const SIDE_STONE2_SPEC = 999;//副石2规格
    const SIDE_STONE2_COLOUR = 89;//副石2色彩
    
    //副石3信息
    const SIDE_STONE3_TYPE = 999;//副石3类型
    const SIDE_STONE3_WEIGHT = 999;//副石3重量(ct)
    const SIDE_STONE3_NUM = 999;//副石3数量
    const SIDE_STONE3_SPEC = 999;//副石3规格

    //其他信息
    const GOODS_COLOR = 77;//货品外部颜色
    const PRODUCT_SIZE = 75;//成品尺寸(mm)
    const TALON_HEAD_TYPE = 90;//爪头形状
    const XIANGQIAN_CRAFT = 81;//镶嵌工艺
    const STONE_TYPE_MO = 241;//莫桑石
    const KEZI = 83;//刻字内容
}