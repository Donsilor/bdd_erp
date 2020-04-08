<?php

namespace addons\Style\common\enums;

/**
 * 材质 枚举
 * @package common\enums
 */
class StoneEnum extends BaseEnum
{
    const POSITION_MAIN = 1;
    const POSITION_DEPUTY = 2;

    const TYPE_DIA = 1;
    const TYPE_BLA = 2;
    /**
     * @return array
     * 
     */
    public static function getMap(): array
    {
        return [

        ];
    }

    public static function getPositionMap(): array
    {
        return [
            self::POSITION_MAIN => '主石',
            self::POSITION_DEPUTY => '副石',

        ];
    }


    public static function getTypeMap(): array
    {
        return [
            self::TYPE_DIA => '钻石',
            self::TYPE_BLA => '蓝宝',

        ];
    }
    
}