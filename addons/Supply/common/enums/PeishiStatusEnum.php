<?php

namespace addons\Supply\common\enums;

use common\enums\BaseEnum;

/**
 * 配石状态
 * @package common\enums
 */
class PeishiStatusEnum extends BaseEnum
{
    const PENDING = 1;
    const IN_PEISHI = 2;
    const HAS_PEISHI = 3;
    const TO_LINGSHI = 4;
    const HAS_LINGSHI = 5;
    const NONE = 9;
    /**
     *
     * @return array
     */
    public static function getMap(): array
    {
        return [                
                self::PENDING =>"待配石",
                self::IN_PEISHI => "配石中",
                self::HAS_PEISHI => "已配石",
                self::TO_LINGSHI => "待领石",
                self::HAS_LINGSHI => "已领石",
                self::NONE => "不需配石",
        ];
    }
    
}