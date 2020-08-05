<?php

namespace addons\Style\common\enums;

/**
 * 材质 枚举
 * @package common\enums
 */
class MaterialEnum extends BaseEnum
{
    const MAT_18K = 1;
    const MAT_PLATINUM = 2;
    const MAT_GOLD = 3;
    const MAT_SILVER = 4;
    const MAT_ALLOY = 5;

    /**
     * @return array
     * 
     */
    public static function getMap(): array
    {
        return [
                self::MAT_18K => "18K",
                self::MAT_PLATINUM => "铂金",
                self::MAT_GOLD => "黄金",
                self::MAT_SILVER => "银",
                self::MAT_ALLOY => "合金 ",

        ];
    }
    
    
}