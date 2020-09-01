<?php

namespace addons\Finance\common\enums;

/**
 * 到账类型
 * @package common\enums
 */
class ArriveStatusEnum extends \common\enums\BaseEnum
{
    const HAS_ARRIVED = 1;
    const NOT_ARRIVED = 0;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::HAS_ARRIVED => '已到账',
            self::NOT_ARRIVED => '未到账',
        ];
    }
    
}