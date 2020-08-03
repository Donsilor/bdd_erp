<?php

namespace addons\Purchase\common\enums;

/**
 * 采购单分类
 * @package common\enums
 */
class PurchaseCateEnum extends BaseEnum
{
    
    const BUSSINSS = 1;
    const COMPANY = 2;
    const ORDER = 3;
    const OTHER = 9;
    /**
     *
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::BUSSINSS => "业务需求",
                self::COMPANY =>'公司备货',
                self::ORDER =>'顾客订单',
                self::OTHER =>'其它',
        ];
    }
    
}