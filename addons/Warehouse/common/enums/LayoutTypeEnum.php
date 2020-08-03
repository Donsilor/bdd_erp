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
    const SILVER_RUBBER = 3;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::SILVER => '银版',
            self::RUBBER => '胶膜板',
            self::SILVER_RUBBER => '银版胶膜板',
        ];
    }


}