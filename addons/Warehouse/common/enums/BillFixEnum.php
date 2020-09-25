<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 单据编号固定编码
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class BillFixEnum extends \common\enums\BaseEnum
{
    const BILL_RK   = 'RK';
    const BILL_CK   = 'CK';


    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::BILL_RK   => '其他入库单',
            self::BILL_CK   => '其他出库单',
        ];
    }

}