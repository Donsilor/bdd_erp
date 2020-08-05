<?php

namespace addons\Sales\common\enums;

/**
 * 是否赠品
 * @package common\enums
 */
class IsGiftEnum extends \common\enums\BaseEnum
{
    
    const YES = 1;
    const NO = 0;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::NO => '非赠品',
                self::YES => '赠品',
        ];
    }
    
}