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
    const CONFIRM   = 3;
    const AUDITED   = 4;
    const AFFIRM   = 5;
    const CANCEL = 9;
    
    /**
     * 采购状态
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::SAVE    => '已保存',
                self::PENDING => '待审核',
                self::CONFIRM => '已审核（业务）',
                self::AUDITED => '已审核（商品部）',
                self::AFFIRM => '已确认',
                self::CANCEL => '已取消',
        ];
    }

    public static function getMapList(): array
    {
        return [
            self::SAVE    => '已保存',
            self::PENDING => '待审核',
            self::CONFIRM => '待审核',
            self::AUDITED => '已审核',
            self::AFFIRM => '已确认',
            self::CANCEL => '已取消',
        ];
    }
    
}