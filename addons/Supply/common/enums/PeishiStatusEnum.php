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
    const DOING = 2;
    const CONFIRM = 3;
    const FINISH = 4;
    const NONE = 5;
    /**
     *
     * @return array
     */
    public static function getMap(): array
    {
        return [                
                self::PENDING =>"待配石",
                self::DOING => "配石中",
                self::CONFIRM => "配石确认",
                self::FINISH => "配石完成",
                self::NONE => "不需配石",
        ];
    }
    
}