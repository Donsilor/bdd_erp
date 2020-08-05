<?php

namespace addons\Sales\common\enums;

/**
 * 客户等级
 * @package common\enums
 */
class LevelEnum extends \common\enums\BaseEnum
{
    const GENERAL = 1;
    const A = 2;
    const B = 3;
    const C = 4;
    const D = 5;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::A => 'A级',
                self::B => 'B级',
                self::C => 'C级',
                self::D => 'D级',
                self::GENERAL => '普通',
        ];
    }
    
}