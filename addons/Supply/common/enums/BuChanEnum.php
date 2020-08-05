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
    const TO_PEILIAO = 4;
    const IN_PEILIAO = 5;
    const TO_PRODUCTION = 6;
    const IN_PRODUCTION = 7;
    const PARTIALLY_SHIPPED = 8;
    const FACTORY = 9;
    const CANCELLED = 10;
    const NO_PRODUCTION = 11;

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
            self::TO_PEILIAO => "待配料",
            self::IN_PEILIAO => "配料中",
            self::TO_PRODUCTION => "待生产",
            self::IN_PRODUCTION => "生产中",
            self::PARTIALLY_SHIPPED => "部分出厂",
            self::FACTORY => "已出厂",
            self::CANCELLED => "已取消",
            self::NO_PRODUCTION => "不需布产",
        ];
    }
    
}