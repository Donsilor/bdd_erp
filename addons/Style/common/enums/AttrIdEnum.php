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
    const FINGER = 38;//手寸（指圈）
    const MATERIAL = 10;//材质（成色）
    const XIANGKOU = 49;//镶口
    const INLAY_METHOD = 58;//镶嵌方式
    const CHAIN_LENGTH = 53;//链长
    const CHAIN_TYPE = 43;//链类型    
    const CHIAN_BUCKLE = 42;//链扣环
    const HEIGHT = 41;//高度（mm）
    const FACEWORK = 57;//表面工艺
    
    //物料
    const MAT_STONE_TYPE = 40;//石料类型
    const MAT_MATERIAL_TYPE = 51;//材质
    
    //钻石信息
    const DIA_CLARITY = 2;//钻石净度
    const DIA_CUT = 4;//钻石切工
    const DIA_CARAT = 59;//钻石大小
    const DIA_SHAPE = 6;//钻石形状 
    const DIA_COLOR = 7;//钻石颜色
    const DIA_FLUORESCENCE = 8;//荧光
    const DIA_CERT_NO = 31;//证书编号
    const DIA_CERT_TYPE = 48;//证书类型
    const DIA_CUT_DEPTH = 32;//切割深度（%）
    const DIA_TABLE_LV = 33;//台宽比（%）
    const DIA_LENGTH = 34;//长度（mm）
    const DIA_WIDTH = 35;//宽度（mm）
    const DIA_ASPECT_RATIO = 36;//长宽比（%）
    const DIA_STONE_FLOOR = 37;//石底层
    const DIA_POLISH = 28;//抛光
    const DIA_SYMMETRY = 29;//对称

    //主石信息
    const MAIN_STONE_TYPE = 56;//主石类型
    const MAIN_STONE_NUM = 65;//主石数量
    
    //副石1信息
    const SIDE_STONE1_TYPE = 60;//副石1类型
    const SIDE_STONE1_COLOR = 46;//副石1颜色
    const SIDE_STONE1_CLARITY = 47;//副石1净度    
    const SIDE_STONE1_WEIGHT = 44;//副石1重量(ct)
    const SIDE_STONE1_NUM = 45;//副石1数量      
    //副石2信息
    const SIDE_STONE2_TYPE = 64;//副石2类型
    const SIDE_STONE2_WEIGHT = 63;//副石2重量(ct)
    const SIDE_STONE2_NUM = 66;//副石2数量
}