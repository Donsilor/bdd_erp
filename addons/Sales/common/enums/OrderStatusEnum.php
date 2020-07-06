<?php

namespace addons\Sales\common\enums;

/**
 * 订单状态
 * @package common\enums
 */
class OrderStatusEnum extends \common\enums\BaseEnum
{

    const PENDIGN = 1;
    const CONFORMED = 2;
    const CLOSE = 3;
    const CANCAEL = 4;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [               
               self::PENDIGN => "待审核",
               self::CONFORMED => "已审核", 
               self::CANCAEL => "已关闭", 
               self::CLOSE => "已取消", 
        ];
    }
    
}