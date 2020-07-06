<?php

namespace addons\Purchase\common\enums;


/**
 *
 * 采购申请单确认状态
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class ApplyConfirmEnum extends \common\enums\BaseEnum
{
    
    const SAVE     = 1;
    const GOODS   = 2;
    const DESIGN    = 3;
    const CONFIRM   = 4;

    
    /**
     * 确认状态
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::SAVE    => '已保存',
                self::GOODS => '待确认（商品部）',
                self::DESIGN => '待确认（设计部）',
                self::CONFIRM => '已确认',

        ];
    }

    
}