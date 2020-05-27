<?php

namespace addons\Purchase\common\enums;

/**
 * 采购单状态
 * @package common\enums
 */
class PurchaseStatusEnum extends BaseEnum
{
    const  SAVED = 1;
    const  PENDING = 2;
    const  COMFIRMED = 3;
    const  CANCELED = 9;
    /**
     * 
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::SAVED => "保存",
            self::PENDING => "待审核",
            self::COMFIRMED => "已审核",                
            self::CANCELED => "已取消",
        ];
    }
    
}