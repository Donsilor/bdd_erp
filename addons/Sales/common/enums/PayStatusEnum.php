<?php

namespace addons\Sales\common\enums;

/**
 * 支付状态
 * @package common\enums
 */
class PayStatusEnum extends \common\enums\BaseEnum
{
    
    const NO_PAY  = 0;
    const HAS_PAY = 1;
    const PART_PAY = 2;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::NO_PAY => "未支付",                
                self::HAS_PAY => "已支付",
                self::PART_PAY => "部分支付",
        ];
    }
    
}