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
                self::CANCEL => '已取消',
        ];
    }
    
}