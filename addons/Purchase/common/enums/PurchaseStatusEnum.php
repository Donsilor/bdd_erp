<?php

namespace addons\Style\common\enums;

/**
 * 采购单状态
 * @package common\enums
 */
class PurchaseStatusEnum extends BaseEnum
{
    const  SAVED = 1;
    const  COMFIRM = 2;
    const  BUCHAN = 3;
    const  IN_PRODUCTION = 4;
    const  PART_SHIPPED = 5;
    const  All_SHIPPED = 6;
    const  FINISHED = 7;
    const  CANCELED = 99;
    /**
     * 
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::SAVED => "待审核",
            self::COMFIRMED => "已审核",
            self::BUCHAN => "已布产",
            self::IN_PRODUCTION => "生产中",
            self::PART_SHIPPED => "部分出厂",
            self::All_SHIPPED => "已出厂",
            self::FINISHED => "已完成",
            self::CANCELED => "已取消",
        ];
    }
    
}