<?php

namespace addons\Purchase\common\enums;


/**
 *
 * 采购申请单状态
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class ApplyStatusEnum extends \common\enums\BaseEnum
{
    
    const SAVE     = 1;
    const PENDING    = 2;
    const DETAIL    = 6;
    const CONFIRM   = 3;
    const AUDITED   = 4;
    const FINISHED   = 5;
    const CANCEL = 9;
    
    /**
     * 采购状态
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::SAVE    => '待提审',
                self::PENDING => '待审核-业务部',
                self::DETAIL => '明细待审(待确认)',
                self::CONFIRM => '待审核-商品部',
                self::AUDITED => '待确认(创建本人确认)',
                self::FINISHED => '已完成',
                self::CANCEL => '已取消',
        ];
    }

    public static function getMapList(): array
    {
        return [
            self::SAVE    => '待提审',
            self::PENDING => '待审核（业务）',
            self::DETAIL => '明细待审(待确认)',
            self::CONFIRM => '待审核（商品）',
            self::AUDITED => '待确认(创建本人确认)',
            self::FINISHED => '已完成',
            self::CANCEL => '已取消',
        ];
    }
    
}