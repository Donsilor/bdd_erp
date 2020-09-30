<?php

namespace addons\Style\common\enums;

/**
 * 起版创建来源
 * @package common\enums
 */
class QibanFromEnum extends \common\enums\BaseEnum
{
    const FROM_HAND = 1;
    const FROM_PURCHASE = 2;
    
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::FROM_HAND => "手动创建",
                self::FROM_PURCHASE => "采购单同步",
        ];
    }
    
}