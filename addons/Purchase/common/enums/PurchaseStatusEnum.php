<?php

namespace addons\Style\common\enums;

/**
 * 表面工艺  枚举
 * @package common\enums
 */
class PurchaseStatusEnum extends BaseEnum
{
    const WAIT_AUDIT = 0;
    const DATA_SYNCH = 2;
    const SYSTEM = 3;
    
    /**
     * 
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::WAIT_AUDIT => "待审核",
            self::DATA_SYNCH => "已审核",
            self::SYSTEM => "不通过",
            
        ];
    }
    
}