<?php

namespace addons\Warehouse\common\enums;

/**
 *
 * 配石类型
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class PeiLiaoWayEnum extends \common\enums\BaseEnum
{
    const COMPANY = 1;
    const FACTORY = 2;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::COMPANY => '公司配',
            self::FACTORY => '工厂配',
        ];
    }

}