<?php

namespace addons\Style\common\enums;

/**
 * 材质 枚举
 * @package common\enums
 */
class MaterialTypeEnum extends BaseEnum
{
    const MAT_18K = '18K';
    const MAT_PT = 'PT';
    const MAT_GOLD = 'GOLD';
    const MAT_SILVER = 'SILVER';
    const MAT_ALLOY = 'ALLOY';
    
    /**
     * @return array
     *
     */
    public static function getMap(): array
    {
        return [
                self::MAT_18K => "18K",
                self::MAT_PT => "铂金",
                self::MAT_GOLD => "黄金",
                self::MAT_SILVER => "银",
                self::MAT_ALLOY => "合金 ",
                
        ];
    }
    
    
    
}