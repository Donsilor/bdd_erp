<?php

namespace addons\Supply\common\enums;

use common\enums\BaseEnum;

/**
 * 订单来源  枚举
 * @package common\enums
 */
class FromTypeEnum extends BaseEnum
{
    const ORDER = 1;
    const PURCHASE = 2;
    
    /**
     * 
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::ORDER => "客订单",
            self::PURCHASE => "采购单",            
        ];
    }
    
}