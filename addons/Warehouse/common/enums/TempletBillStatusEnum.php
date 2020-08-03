<?php

namespace addons\Warehouse\common\enums;

/**
 *
 * 样板单据状态
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class TempletBillStatusEnum extends \common\enums\BaseEnum
{
    
    const SAVE     = 1;
    const PENDING    = 2;
    const CONFIRM   = 3;
    const CANCEL = 9;    
    
    /**
     * 单据通用状态
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::SAVE    => '已保存',
            self::PENDING => '待审核',
            self::CONFIRM => '已审核',
            self::CANCEL => '已取消',
        ];
    }

}