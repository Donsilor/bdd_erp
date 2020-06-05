<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 结算状态
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class AccountTypeEnum extends \common\enums\BaseEnum
{
    const ACCOUNTS  = 0;
    const PAYMENT    = 1;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::ACCOUNTS      => '记账',
            self::PAYMENT     => '已付款',
        ];
    }

}