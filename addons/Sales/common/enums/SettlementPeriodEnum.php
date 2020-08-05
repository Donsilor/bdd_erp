<?php

namespace addons\Sales\common\enums;

/**
 * 结算周期
 * @package common\enums
 */
class SettlementPeriodEnum extends \common\enums\BaseEnum
{
    
    const NOW  = 1;
    const DAILY = 2;
    const WEEKS = 3;
    const MONTHLY = 4;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::NOW => "现结",
                self::DAILY => "日结",
                self::WEEKS => "周结",
                self::MONTHLY => "月结",
        ];
    }
    
}