<?php

namespace addons\Warehouse\common\enums;

/**
 *
 * 版式类型
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class LayoutTypeEnum extends \common\enums\BaseEnum
{
    const SILVER = 1;
    const RUBBER = 2;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::SILVER => '银版',
            self::RUBBER => '胶膜板',
        ];
    }


}