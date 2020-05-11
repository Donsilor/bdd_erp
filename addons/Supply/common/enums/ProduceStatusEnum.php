<?php

namespace addons\Supply\common\enums;

use common\enums\BaseEnum;
/**
 * 表面工艺  枚举
 * @package common\enums
 */
class ProduceStatusEnum extends BaseEnum
{
    const PENDING_REVIEW = 1;
    const TO_ALLOCATED = 2;
    const TO_PRODUCED = 3;
    const IN_PRODUCTION = 4;
    const TO_FACTORY = 5;
    const PARTIALLY_SHIPPED = 6;
    const FACTORY = 7;
    /**
     * 
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::PENDING_REVIEW => "待审核",
            self::TO_ALLOCATED => "待分配",
            self::TO_PRODUCED => "待生产",
            self::IN_PRODUCTION => "生产中",
            self::TO_FACTORY => "待出厂",
            self::PARTIALLY_SHIPPED => "部分出厂",
            self::FACTORY => "已出厂",

        ];
    }
    
}