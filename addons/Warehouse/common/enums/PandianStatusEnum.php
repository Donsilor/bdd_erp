<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 盘点状态
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class PandianStatusEnum extends \common\enums\BaseEnum
{
    const SAVE = 1;
    const LOSS = 2;
    const PROFIT = 3;
    const NORMAL = 4;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::SAVE => '待盘点',
                self::LOSS => '盘亏',
                self::PROFIT => '盘盈',
                self::NORMAL => '正常', 
        ];
    }
    
    public static function getLossProfixIds()
    {
        return [self::LOSS,self::PROFIT];
    }
    
}