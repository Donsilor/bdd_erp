<?php

namespace addons\Warehouse\common\enums;

/**
 *
 * 盘点调整原因
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class AdjustReasonEnum extends \common\enums\BaseEnum
{
    
    const SAVE     = 0;
    const HAS_SOLD   = 1;
    const NORMAL_WASTE    = 2;
    const OTHER  = 3;
    
    /**
     * 盘点调整原因
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::SAVE    => '未调整',
            self::HAS_SOLD => '已销售',
            self::NORMAL_WASTE => '正常耗损',
            self::OTHER => '其它'
        ];
    }
    
}