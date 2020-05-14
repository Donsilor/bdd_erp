<?php

namespace addons\Style\common\enums;

/**
 * 采购单状态
 * @package common\enums
 */
class PurchaseStatusEnum extends BaseEnum
{
    const  SAVED = "待审核";
    const  COMFIRM = "已审核";
    const  BUCHAN = "已布产";
    const  IN_PRODUCTION = "生产中";
    const  PART_SHIPPED = "部分出厂";
    const  All_SHIPPED = "已出厂";
    const  FINISHED = "已完成";
    const  CANCELED = "已取消";
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