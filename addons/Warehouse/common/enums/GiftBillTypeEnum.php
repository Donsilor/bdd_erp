<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 仓储单据类型
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class GiftBillTypeEnum extends \common\enums\BaseEnum
{
    const GIFT_PURCHASE = 1;
    const GIFT_ORDER = 2;
    const GIFT_OTHER = 3;




    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::GIFT_PURCHASE   => '采购单',
            self::GIFT_ORDER   => '客订单',
            self::GIFT_OTHER   => '其它',

        ];
    }

}