<?php

namespace addons\Supply\common\enums;

use common\enums\BaseEnum;

/**
 * 配料状态
 * @package common\enums
 */
class PeiliaoStatusEnum extends BaseEnum
{
    
    const PENDING = 1;
    const IN_PEILIAO = 2;
    const HAS_PEILIAO = 3;
    const TO_LINGLIAO = 4;
    const HAS_LINGLIAO = 5;
    const NONE = 9;
    /**
     *
     * @return array
     */
    public static function getMap(): array
    {
        return [                
                self::PENDING => "待配料",
                self::IN_PEILIAO => "配料中",
                self::HAS_PEILIAO => "已配料",
                self::TO_LINGLIAO => "待领料",
                self::HAS_LINGLIAO => "已领料",
                self::NONE => "不需配料",
        ];
    }
    
}