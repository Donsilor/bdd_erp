<?php

namespace addons\Sales\common\enums;

use common\enums\BaseEnum;

/**
 * 支付方式
 * @package common\enums
 */
class PayTypeEnum extends BaseEnum
{
    const UNKNOW = 0;
    const PAYPAL = 1;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [              
              self::PAYPAL =>'Paypal',
              self::UNKNOW =>'未知' 
        ];
    }
    
}