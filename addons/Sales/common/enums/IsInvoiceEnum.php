<?php

namespace addons\Sales\common\enums;

/**
 * 是否开发票
 * @package common\enums
 */
class IsInvoiceEnum extends \common\enums\BaseEnum
{
    
    const YES = 1;
    const NO = 0;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [                
                self::YES => '开发票',
                self::NO => '不开发票',
        ];
    }
    
}