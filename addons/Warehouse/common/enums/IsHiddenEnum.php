<?php

namespace addons\Warehouse\common\enums;

/**
 *
 * 是否隐藏
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class IsHiddenEnum extends \common\enums\BaseEnum
{
    const NO = 0;
    const YES = 1;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::NO => '否',
            self::YES => '是',
        ];
    }

}