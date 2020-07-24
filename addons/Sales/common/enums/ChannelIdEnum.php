<?php

namespace addons\Sales\common\enums;

/**
 * 渠道ID
 * @package common\enums
 */
class ChannelIdEnum extends \common\enums\BaseEnum
{
    
    const GP = 3;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::GP => '国际批发',
        ];
    }
    
}