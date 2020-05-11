<?php

namespace addons\Supply\common\enums;

use common\enums\BaseEnum;

/**
 * 订单来源  枚举
 * @package common\enums
 */
class FromTypeEnum extends BaseEnum
{
    const ORDER_FROM = 1;
    const PURCHASE_FROM = 2;
    
    /**
     * 
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::ORDER_FROM => "订单",
            self::PURCHASE_FROM => "采购单",
            
        ];
    }
    
}