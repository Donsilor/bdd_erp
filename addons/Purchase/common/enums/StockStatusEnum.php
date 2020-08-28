<?php

namespace addons\Purchase\common\enums;

/**
 *
 * 入库状态
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class StockStatusEnum extends \common\enums\BaseEnum
{
    
    const SAVE     = 0;
    const IN_STOCK    = 1;
    const HAS_STOCK   = 2;
    
    /**
     * 状态
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::SAVE    => '未入库',
            self::IN_STOCK => '部分入库',
            self::HAS_STOCK => '已入库',
        ];
    }

}