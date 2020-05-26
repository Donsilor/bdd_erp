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
    const PENDING    = 2;
    const CONFIRM   = 3;
    const CANCEL = 4;
    
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::SAVE      => '保存',
                self::PENDING     => '待审核',
                self::CONFIRM    => '已审核',
                self::CANCEL  => '已取消',
        ];
    }
    
}