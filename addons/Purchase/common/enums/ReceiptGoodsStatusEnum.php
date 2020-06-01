<?php

namespace addons\Purchase\common\enums;

/**
 * 采购收货单货品状态  枚举
 * @package common\enums
 */
class ReceiptGoodsStatusEnum extends BaseEnum
{
    const SAVE = 0;
    const IQC_ING = 1;
    const IQC_PASS = 2;
    const IQC_NO_PASS = 3;
    const FACTORY_ING = 4;
    const FACTORY = 5;
    const SCRAP = 6;
    const CANCEL = 10;

    /**
     *
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::SAVE => "保存",
            self::IQC_ING => "质检中",
            self::IQC_PASS => "质检通过",
            self::IQC_NO_PASS => "质检未过",
            self::FACTORY_ING => "返厂中",
            self::FACTORY => "已返厂",
            self::SCRAP => "报废",
            self::CANCEL => "取消",
        ];
    }
    
}