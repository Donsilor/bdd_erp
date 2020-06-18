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
    const TO_CONFIRM = 3;
    const FINISHED = 4;
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
                self::TO_CONFIRM => "配料确认",
                self::FINISHED => "配料完成",
                self::NONE => "不需配料",
        ];
    }
    
}