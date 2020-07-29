<?php

namespace addons\Sales\common\enums;

/**
 * 客户等级
 * @package common\enums
 */
class CustomerLevelEnum extends \common\enums\BaseEnum
{
    const GENERAL = 1;
    const LELVEL_A = 2;
    const LELVEL_B = 3;
    const LELVEL_C = 4;
    const LELVEL_D = 5;
    
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::LELVEL_A => 'A级',
                self::LELVEL_B => 'B级',
                self::LELVEL_C => 'C级',
                self::LELVEL_D => 'D级',
                self::GENERAL => '普通',
        ];
    }
    
}