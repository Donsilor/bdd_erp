<?php

namespace addons\Warehouse\common\enums;

/**
 *
 * 配件方式
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class PartsWayEnum extends \common\enums\BaseEnum
{
    const NO_PEI = 0;
    const COMPANY = 1;
    const FACTORY = 2;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::NO_PEI => '不需配件',
            self::COMPANY => '公司配',
            self::FACTORY => '工厂配',
        ];
    }

}