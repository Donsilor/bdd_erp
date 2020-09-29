<?php

namespace addons\Warehouse\common\enums;

/**
 *
 * 结算材质
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class PayMaterialEnum extends \common\enums\BaseEnum
{
    const GOLD       = 1;
    const SILVER     = 2;
    const PLATINUM   = 3;
    const PALLADIUM  = 4;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::GOLD          => '金',
            self::SILVER        => '银',
            self::PLATINUM      => '铂金',
            self::PALLADIUM     => '钯金',
        ];
    }

}