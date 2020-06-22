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
    const BILL_TYPE_O   = 'O';
    const BILL_TYPE_WF  = 'WF';
    const BILL_TYPE_R   = 'R';
    const BILL_TYPE_WX  = 'WX';
    const BILL_TYPE_C   = 'C';

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::BILL_TYPE_L   => '收货单',
            self::BILL_TYPE_S   => '销售单',
            self::BILL_TYPE_M   => '调拨单',
            self::BILL_TYPE_W   => '盘点单',
            self::BILL_TYPE_B   => '退货返厂单',
            self::BILL_TYPE_T   => '其它收货单',
            self::BILL_TYPE_O   => '维修退货单',
            self::BILL_TYPE_WF  => '维修调拨单',
            self::BILL_TYPE_R   => '维修发货单',
            self::BILL_TYPE_WX  => '维修单',
            self::BILL_TYPE_C   => '其他出库单',
        ];
    }

}