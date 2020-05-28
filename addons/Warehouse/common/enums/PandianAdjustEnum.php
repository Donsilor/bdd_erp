<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 盘点调整状态
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class PandianAdjustEnum extends \common\enums\BaseEnum
{
    
    const SAVE     = 0;
    const ON_WAY    = 1;
    const HAS_SOLD   = 2;
    
    /**
     * 盘点调整状态
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::SAVE    => '未调整',
                self::ON_WAY => '在途',
                self::HAS_SOLD => '已销售',
        ];
    }
    
}