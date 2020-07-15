<?php

namespace addons\Sales\common\enums;

/**
 * 是否通过
 * @package common\enums
 */
class IsPassEnum extends \common\enums\BaseEnum
{
    const NO = 0;
    const YES = 1;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::NO => '不通过',
                self::YES => '通过',
        ];
    }
    
}