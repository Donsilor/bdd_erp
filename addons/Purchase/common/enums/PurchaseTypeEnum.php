<?php

namespace addons\Purchase\common\enums;

/**
 * 采购单类型
 * @package common\enums
 */
class PurchaseTypeEnum extends BaseEnum
{
    
    const GOODS = 1;
    const MATERIAL_STONE = 2;
    const MATERIAL_GOLD = 3;
    const MATERIAL_PARTS = 4;
    const MATERIAL_GIFT = 5;

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
                self::MATERIAL_PARTS =>'配件采购',
                self::MATERIAL_GIFT =>'赠品采购',
        ];
    }
    
}