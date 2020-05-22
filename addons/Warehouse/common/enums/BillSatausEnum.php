<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 仓储单据类型
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class BillStatusEnum extends \common\enums\BaseEnum
{
    const BILL_TYPE_L = 'L';
    const BILL_TYPE_S = 'S';

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::BILL_TYPE_L => '收货单',
            self::BILL_TYPE_S => '销售单',
        ];
    }

}