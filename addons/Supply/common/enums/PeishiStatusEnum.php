<?php

namespace addons\Supply\common\enums;

use common\enums\BaseEnum;

/**
 * 配石状态
 * @package common\enums
 */
class PeishiStatusEnum extends BaseEnum
{
    const NONE = 1;
    const PENDING = 2;
    const DOING = 3;
    const CONFIRM = 4;
    const FINISH = 5;
    
    /**
     *
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::NONE => "不需配石",
                self::PENDING =>"待配石",
                self::DOING => "配石中",
                self::CONFIRM => "配石确认",
                self::FINISH => "配石完成",
        ];
    }
    
}