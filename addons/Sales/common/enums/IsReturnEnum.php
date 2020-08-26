<?php

namespace addons\Sales\common\enums;

/**
 * 商品退货状态
 * @package common\enums
 */
class IsReturnEnum extends \common\enums\BaseEnum
{
    
    const SAVE = 0;
    const APPLY = 1;
    const HAS_RETURN = 2;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::SAVE => '未操作',
            self::APPLY => '退款中',
            self::HAS_RETURN => '已退款',
        ];
    }
    
}