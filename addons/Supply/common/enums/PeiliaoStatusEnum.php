<?php

namespace addons\Supply\common\enums;

use common\enums\BaseEnum;

/**
 * 配料状态
 * @package common\enums
 */
class PeiliaoStatusEnum extends BaseEnum
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
                self::NONE => "不需配料",
                self::PENDING => "待配料",
                self::DOING => "配料中",
                self::CONFIRM => "配料确认",
                self::FINISH => "配料完成",
        ];
    }
    
}