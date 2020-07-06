<?php

namespace addons\Sales\common\enums;

/**
 * 订单类型
 * @package common\enums
 */
class OrderTypeEnum extends \common\enums\BaseEnum
{
    
    const STOCK = 1;
    const FUTURE = 2;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::STOCK => "现货单",
                self::FUTURE => "期货单",
        ];
    }
    
}