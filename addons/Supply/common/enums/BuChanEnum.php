<?php

namespace addons\Supply\common\enums;

use common\enums\BaseEnum;

/**
 * 表面工艺  枚举
 * @package common\enums
 */
class BuChanEnum extends BaseEnum
{
    const INITIALIZATION = 1;
    const TO_CONFIRMED = 2;
    const ASSIGNED = 3;
    const IN_PRODUCTION = 4;
    const PARTIALLY_SHIPPED = 5;
    const FACTORY = 6;
    const CANCELLED = 7;
    const NO_PRODUCTION = 7;

    /**
     * 
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::INITIALIZATION => "初始化",
            self::TO_CONFIRMED => "已分配待确认",
            self::ASSIGNED => "已分配",
            self::IN_PRODUCTION => "生产中",
            self::PARTIALLY_SHIPPED => "部分出厂",
            self::FACTORY => "已出厂",
            self::CANCELLED => "已取消",
            self::NO_PRODUCTION => "不需布产",

        ];
    }
    
}