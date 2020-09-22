<?php

namespace addons\Finance\common\enums;

/**
 * 到账类型
 * @package common\enums
 */
class ArriveTypeEnum extends \common\enums\BaseEnum
{
    const POSTPONED = 1;
    const ON_TIME = 2;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::POSTPONED => "延期到账",
            self::ON_TIME => "实时到账",
        ];
    }
    
}