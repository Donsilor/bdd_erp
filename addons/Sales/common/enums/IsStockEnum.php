<?php

namespace addons\Sales\common\enums;

/**
 * 是否现货
 * @package common\enums
 */
class IsStockEnum extends \common\enums\BaseEnum
{
    
    const YES = 1;
    const NO = 0;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::NO => '期货',
                self::YES => '现货',
        ];
    }
    
}