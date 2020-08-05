<?php

namespace addons\Sales\common\enums;

/**
 * 发货状态
 * @package common\enums
 */
class DeliveryStatusEnum extends \common\enums\BaseEnum
{
    
    const SAVE = 0;
    const TO_SEND = 1;
    const HAS_SEND = 2;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::SAVE => "未发货",
                self::TO_SEND => "待发货",
                self::HAS_SEND => "已发货",
        ];
    }
    
}