<?php

namespace addons\Purchase\common\enums;

/**
 * 采购单类型
 * @package common\enums
 */
class PurchaseTypeEnum extends BaseEnum
{
    const APPLY = 10;
    const PURCHASE = 20;
    const OTHER = 9;
    
    const GOODS = 1;
    const MATERIAL_STONE = 2;
    const MATERIAL_GOLD = 3;
    /**
     *
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::GOODS => "商品采购",
                self::MATERIAL_STONE =>'石料采购',
                self::MATERIAL_GOLD =>'金料采购',
                
                self::APPLY => "业务申请",
                self::PURCHASE =>'公司备货',
                self::OTHER =>'其他',
        ];
    }
    
}