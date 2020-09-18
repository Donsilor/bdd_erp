<?php

namespace addons\Warehouse\common\enums;

/**
 *
 * 是否自动计算价格
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class IsAutoCalculateEnum extends \common\enums\BaseEnum
{
    const YES = 0;
    const NO = 1;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::YES => '是',
            self::NO => '否',
        ];
    }

}