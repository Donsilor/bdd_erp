<?php

namespace addons\Purchase\common\enums;

/**
 * 采购单类型
 * @package common\enums
 */
class ApplyFromEnum extends BaseEnum
{
    
    const BUSINESS_APPLY = 1;
    const ORDER = 2;
    /**
     *
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::BUSINESS_APPLY => "业务申请",
                self::ORDER =>'顾客订单',

        ];
    }
    
}