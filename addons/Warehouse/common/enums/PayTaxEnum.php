<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 是否含税金额
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class PayTaxEnum extends \common\enums\BaseEnum
{
    const NO_TAX    = 0;
    const YES_TAX   = 1;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::NO_TAX      => '不含税',
            self::YES_TAX     => '含税',
        ];
    }

}