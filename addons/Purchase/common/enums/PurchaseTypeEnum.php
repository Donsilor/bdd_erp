<?php

namespace addons\Purchase\common\enums;

/**
 * 采购单类型
 * @package common\enums
 */
class PurchaseTypeEnum extends BaseEnum
{
    const GOODS = 1;
    const MATERIAL = 2;
    
    /**
     *
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::GOODS => "商品采购",
                self::MATERIAL => "物料采购",                
        ];
    }
    
}