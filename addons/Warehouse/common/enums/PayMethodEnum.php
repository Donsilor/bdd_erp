<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 结算方式
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class PayMethodEnum extends \common\enums\BaseEnum
{
    const TALLY     = 1;
    const PAID      = 2;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::TALLY     => '记账',
            self::PAID      => '已付款',
        ];
    }

}