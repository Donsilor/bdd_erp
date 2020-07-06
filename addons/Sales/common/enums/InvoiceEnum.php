<?php

namespace addons\Sales\common\enums;

/**
 * 客户来源
 * @package common\enums
 */
class InvoiceEnum extends \common\enums\BaseEnum
{
    
    const YES = 1;
    const NO = 0;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::NO => '不开发票',
                self::YES => '开发票',                
        ];
    }
    
}