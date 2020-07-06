<?php

namespace addons\Sales\common\enums;

/**
 * 支付类型
 * @package common\enums
 */
class PayTypeEnum extends \common\enums\BaseEnum
{
    
    const STOCK = 1;
    const FUTURE = 2;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
               
        ];
    }
    
}