<?php

namespace addons\Style\common\enums;

/**
 * 款式性别  枚举
 * @package common\enums
 */
class StyleSexEnum extends \common\enums\BaseEnum
{
    const MAN = 1;
    const WOMEN = 2;
    const COMMON = 3;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::MAN => '男款',
                self::WOMEN => '女款',
                self::COMMON => '中性款',
        ];
    }
    
}