<?php

namespace addons\Sales\common\enums;


/**
 *
 * 退款单据状态
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class ReturnStatusEnum extends \common\enums\BaseEnum
{
    
    const SAVE     = 1;
    const PENDING    = 2;
    const CONFIRM   = 3;
    
    const CANCEL = 9;    
    
    /**
     * 退款单据状态
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::SAVE    => '待提审',
            self::PENDING => '待确认',
            self::CONFIRM => '退款完成',
            self::CANCEL => '已取消',
        ];
    }

}