<?php

namespace addons\Gdzb\common\enums;

/**
 * 婚姻
 * @package common\enums
 */
class MarriageEnum extends \common\enums\BaseEnum
{

    const UNKNOWN = 0;
    const MARRIED = 1;
    const SPINSTERHOOD = 2;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::SPINSTERHOOD => '未婚',
            self::MARRIED => '已婚',
            self::UNKNOWN => '未知',
        ];
    }

}