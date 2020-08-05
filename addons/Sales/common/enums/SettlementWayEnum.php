<?php

namespace addons\Sales\common\enums;

/**
 * 结算方式
 * @package common\enums
 */
class SettlementWayEnum extends \common\enums\BaseEnum
{
    
    const ACCOUNT  = 1;
    const CASH = 2;
    const TRANSFER = 3;
    const OTHER = 10;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::ACCOUNT => "对公账户",
                self::CASH => "现金",
                self::TRANSFER => "转账",
                self::OTHER => "其他",
        ];
    }
    
}