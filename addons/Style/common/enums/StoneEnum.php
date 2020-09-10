<?php

namespace addons\Style\common\enums;

/**
 * 材质 枚举
 * @package common\enums
 */
class StoneEnum extends BaseEnum
{
    const POSITION_MAIN = 1;
    const POSITION_DEPUTY_1 = 2;
    const POSITION_DEPUTY_2 = 3;
    const POSITION_DEPUTY_3 = 4;

    const NOTHING = 1;
    const ROUND_DRILL = 2;
    const HETERO_DRILL = 3;
    const COLOR_DIAMOND = 4;
    const PEARL = 5;
    const EMERALD = 6;
    const RUBY = 7;
    const SAPPHIRE = 8;
    const NEPHRITE = 9;

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
            self::POSITION_DEPUTY_1 => '副石1',
            self::POSITION_DEPUTY_2 => '副石2',
            self::POSITION_DEPUTY_3 => '副石3',

        ];
    }


    public static function getTypeMap(): array
    {
        return [
            self::NOTHING => '无',
            self::ROUND_DRILL => '圆钻',
            self::HETERO_DRILL => '异形钻',
            self::COLOR_DIAMOND => '彩钻',
            self::PEARL => '珍珠',
            self::EMERALD => '翡翠',
            self::RUBY => '红宝石',
            self::SAPPHIRE => '蓝宝石',
            self::NEPHRITE => '和田玉',

        ];
    }
    
}