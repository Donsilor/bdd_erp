<?php

namespace addons\Style\common\enums;

/**
 * 材质 枚举
 * @package common\enums
 */
class StonePositionEnum extends BaseEnum
{
    const MAIN_STONE = 1;
    const SECOND_STONE1 = 2;
    const SECOND_STONE2 = 3;
    const SECOND_STONE3 = 4;    
    
    /**
     * @return array
     *
     */
    public static function getMap(): array
    {
        return [
                self::MAIN_STONE =>'主石',
                self::SECOND_STONE1 =>'副石1',
                self::SECOND_STONE2 =>'副石2',
                self::SECOND_STONE3 =>'副石3',
        ];
    }  
    
}