<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 盘点单据状态
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class BillWStatusEnum extends \common\enums\BaseEnum
{
    const SAVE     = 1;
    const FINISHED   = 2;
    
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::SAVE    => '盘点中',
                self::FINISHED   => '盘点结束',                
        ];
    }
    
}