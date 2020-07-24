<?php

namespace common\enums;

/**
 * 流程类型
 *
 * Class StatusEnum
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class FlowCateEnum extends BaseEnum
{
    const SUPPLY_MOD = 1;//生产管理
    const PURCHASE_MOD = 2;//采购管理
    const STYLE_MOD = 3;//款式管理
    const WAREHOUSE_MOD = 4;//销售管理
    const SALES_MOD = 5;//销售管理
    const FINANCE_MOD = 6;//财务管理

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::SUPPLY_MOD => "生产管理",
                self::PURCHASE_MOD => "采购管理",
                self::STYLE_MOD => "款式管理",
                self::WAREHOUSE_MOD => "仓储管理",
                self::SALES_MOD => "销售管理",
                self::FINANCE_MOD => "财务管理",
        ];
    }   

    
}