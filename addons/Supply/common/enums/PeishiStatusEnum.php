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
                self::PENDING =>"待配石",
                self::IN_PEISHI => "配石中",
                self::TO_CONFIRM => "配石确认",
                self::FINISHED => "配石完成",
                self::NONE => "不需配石",
        ];
    }
    
}