<?php

namespace common\enums;

/**
 * 货币枚举
 *
 * Class CurrencyEnum
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class CurrencyEnum extends BaseEnum
{
    const CNY = 'CNY';
    const HKD = 'HKD';
    const USD = 'USD';
    
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::CNY=> 'CNY',
            self::HKD=> 'HKD',
            self::USD=> 'USD',
        ];
    }
}