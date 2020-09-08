<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 仓储单据类型
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class BillTypeEnum extends \common\enums\BaseEnum
{
    const BILL_TYPE_L   = 'L';
    const BILL_TYPE_S   = 'S';
    const BILL_TYPE_M   = 'M';
    const BILL_TYPE_W   = 'W';
    const BILL_TYPE_B   = 'B';
    const BILL_TYPE_T   = 'T';
    const BILL_TYPE_C   = 'C';
    const BILL_TYPE_A   = 'A';
    const BILL_TYPE_J   = 'J';
    const BILL_TYPE_D   = 'D';
    const BILL_TYPE_WX  = 'WX';


    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::BILL_TYPE_L   => '入库单',
            self::BILL_TYPE_S   => '销售单',
            self::BILL_TYPE_M   => '调拨单',
            self::BILL_TYPE_W   => '盘点单',            
            self::BILL_TYPE_J   => '借货单',
            self::BILL_TYPE_A   => '货品调整单',
            self::BILL_TYPE_D   => '销售退货单',
            self::BILL_TYPE_B   => '退货返厂单',
            self::BILL_TYPE_T   => '其它收货单',
            self::BILL_TYPE_C   => '其它出库单',
            self::BILL_TYPE_WX  => '维修单',

        ];
    }

}