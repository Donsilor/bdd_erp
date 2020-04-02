<?php

namespace addons\Style\common\enums;

/**
 * 属性用途枚举
 * Class AttrUsageEnum
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class AttrUsageEnum extends \common\enums\BaseEnum
{
    const USAGE_ALL = 0;
    const USAGE_BASE = 1;
    const USAGE_SEARCH = 2;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::USAGE_BASE => '基础属性',
                self::USAGE_SEARCH => '搜索属性',
                self::USAGE_ALL => '基础+搜索',
        ];
    }
}