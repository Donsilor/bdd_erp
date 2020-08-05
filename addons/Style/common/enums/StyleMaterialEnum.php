<?php

namespace addons\Style\common\enums;

/**
 * 款式材质 枚举（编款用）
 * @package common\enums
 */
class StyleMaterialEnum extends BaseEnum
{
    const GOLD = 1;
    const SILVER = 2;
    const COPPER = 3;
    const ALLOY = 4; 
    const OTHER = 0;
    /**
     * @return array
     *
     */
    public static function getMap(): array
    {
        return [
                self::GOLD => "金",
                self::SILVER => "银",
                self::COPPER => "铜",
                self::ALLOY => "合金",
                self::OTHER => "其它",
        ];
    }
}