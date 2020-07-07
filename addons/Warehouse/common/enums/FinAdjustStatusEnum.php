<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 财务调整状态
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class FinAdjustStatusEnum extends \common\enums\BaseEnum
{
    
    const SAVE     = 0;
    const LOSS = 1;
    const PROFIT = 2;
    const NORMAL = 3;
    
    /**
     * 盘点调整状态
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::SAVE    => '未调整',
                self::LOSS => '调整盘亏',
                self::PROFIT => '调整盘盈',
                self::NORMAL => '调整正常',
        ];
    }
    
}