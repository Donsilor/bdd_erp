<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 维修类型
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class RepairTypeEnum extends \common\enums\BaseEnum
{
    const NEW_GOODS     = 1;
    const AFTER_SALE    = 2;
    const WAREHOUSE     = 3;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::NEW_GOODS     => '新货维修',
            self::AFTER_SALE    => '售后维修',
            self::WAREHOUSE     => '库房维修',
        ];
    }

}