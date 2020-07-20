<?php

namespace addons\Style\common\enums;

/**
 * 加工费类型 枚举
 * @package common\enums
 */
class FactoryFeeEnum extends \common\enums\BaseEnum
{
    const BASIC_GF = 1;
    const INLAID_GF = 2;
    const PARTS_GF = 3;
    const TECHNOLOGY_GF = 4;
    /**
     * @return array
     * 
     */
    public static function getMap(): array
    {
        return [
                self::BASIC_GF => "基本工费",
                self::INLAID_GF => "镶石工费",
                self::PARTS_GF => "配件工费",
                self::TECHNOLOGY_GF => "工艺工费",

        ];
    }   
    
}