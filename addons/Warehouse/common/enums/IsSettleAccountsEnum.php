<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 单据结价状态
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class IsSettleAccountsEnum extends \common\enums\BaseEnum
{
    const NO_SETTLEMENT  = 0;
    const YES_SETTLEMENT    = 1;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::NO_SETTLEMENT      => '未结价',
            self::YES_SETTLEMENT     => '已结价',
        ];
    }

}