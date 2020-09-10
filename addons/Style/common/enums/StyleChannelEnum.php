<?php

namespace addons\Style\common\enums;

/**
 * 款式渠道  枚举
 * @package common\enums
 */
class StyleChannelEnum extends \common\enums\BaseEnum
{
    const WHOLESALE = 3;
    const CROSS_BORDER = 9;
    const INLAND = 16;


    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::WHOLESALE => '国际批发',
            self::CROSS_BORDER => '跨境电商',
            self::INLAND => '国内电商',
        ];
    }

    /**
     * @return array
     */
    public static function getCodeMap(): array
    {
        return [
            self::WHOLESALE => 'K',
            self::CROSS_BORDER => 'B',
            self::INLAND => 'H',
        ];
    }
}