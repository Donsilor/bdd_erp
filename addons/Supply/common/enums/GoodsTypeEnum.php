<?php

namespace addons\Supply\common\enums;

use common\enums\BaseEnum;

/**
 * 货品类型  枚举
 * @package common\enums
 */
class GoodsTypeEnum extends BaseEnum
{
    const COMMODITY = 1;
    const RAW_MATERIAL  = 2;
//    const GIVEAWAY  = 3;
//    const PACKAGING_MATERIALS  = 4;

    /**
     * 
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::COMMODITY => "商品",
            self::RAW_MATERIAL => "原料",
//            self::GIVEAWAY => "赠品",
//            self::PACKAGING_MATERIALS => "包装材料",

        ];
    }
    
}