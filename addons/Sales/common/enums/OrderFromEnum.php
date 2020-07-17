<?php

namespace addons\Sales\common\enums;

/**
 * 录单来源
 * @package common\enums
 */
class OrderFromEnum extends \common\enums\BaseEnum
{
    const FROM_HAND = 1;
    const FROM_BDD = 2;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::FROM_HAND => "手动创建",
                self::FROM_BDD => "BDD同步",
        ];
    }
    
}