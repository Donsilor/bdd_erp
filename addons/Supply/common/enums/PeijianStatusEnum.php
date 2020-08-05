<?php

namespace addons\Supply\common\enums;

use common\enums\BaseEnum;

/**
 * 配件状态
 * @package common\enums
 */
class PeijianStatusEnum extends BaseEnum
{
    const PENDING = 1;
    const IN_PEIJIAN = 2;
    const HAS_PEIJIAN = 3;
    const TO_LINGJIAN = 4;
    const HAS_LINGJIAN = 5;
    const NONE = 9;
    /**
     *
     * @return array
     */
    public static function getMap(): array
    {
        return [                
                self::PENDING =>"待配件",
                self::IN_PEIJIAN => "配件中",
                self::HAS_PEIJIAN => "已配件",
                self::TO_LINGJIAN => "待领件",
                self::HAS_LINGJIAN => "已领件",
                self::NONE => "不需配件",
        ];
    }
    
}