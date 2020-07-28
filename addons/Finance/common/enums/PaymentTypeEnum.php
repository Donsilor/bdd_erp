<?php

namespace addons\Finance\common\enums;

/**
 * 所属项目
 * @package common\enums
 */
class PaymentTypeEnum extends \common\enums\BaseEnum
{
    const PREPAID = 1;
    const SCHEDULE = 2;
    const SETTLEMENT = 2;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::PREPAID => "预付",
            self::SCHEDULE => "进度",
            self::SETTLEMENT => "结算",
        ];
    }
    
}