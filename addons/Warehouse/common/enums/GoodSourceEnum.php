<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 仓储单据类型
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class GoodSourceEnum extends \common\enums\BaseEnum
{
    const QUICK_STORAGE = 1;
    const PURCHASE_STORAGE = 2;
    const ORDER_STORAGE = 3;
    const IMPORT_STORAGE = 4;



    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::QUICK_STORAGE   => '快捷入库',
            self::PURCHASE_STORAGE   => '采购单采购',
            self::ORDER_STORAGE   => '客订单采购',
            self::IMPORT_STORAGE   => '批量导入',
        ];
    }

}