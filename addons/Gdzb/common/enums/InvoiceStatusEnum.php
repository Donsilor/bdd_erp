<?php

namespace addons\Gdzb\common\enums;

use common\enums\BaseEnum;

/**
 * 发发票状态
 * @package common\enums
 */
class InvoiceStatusEnum extends BaseEnum
{
    const NO_INVOICE = 1;
    const TO_INVOICE = 2;
    const INVOICED = 3;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [              
              self::NO_INVOICE =>'不需发票',
              self::TO_INVOICE =>'待开发票',
              self::INVOICED =>'已开发票',

        ];
    }
    
}