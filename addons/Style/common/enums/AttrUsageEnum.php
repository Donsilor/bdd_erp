<?php

namespace addons\Style\common\enums;

/**
 * 属性用途枚举
 * Class AttrUsageEnum
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class AttrUsageEnum extends BaseEnum
{
    const TYPE_ALL = 0;
    const TYPE_BASE = 1;
    const TYPE_SEARCH = 2;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::TYPE_BASE => '基础属性',
                self::TYPE_SEARCH => '搜索属性',
                self::TYPE_ALL => '基础+搜索',
        ];
    }
}