<?php

namespace addons\Purchase\common\enums;

/**
 * 采购单类型
 * @package common\enums
 */
class PurchaseTypeEnum extends BaseEnum
{
    const APPLY = 1;
    const PURCHASE = 2;
    const OTHER = 9;
    /**
     *
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::APPLY => "业务申请",
                self::PURCHASE =>'公司备货',
                self::OTHER =>'其他',
        ];
    }
    
}