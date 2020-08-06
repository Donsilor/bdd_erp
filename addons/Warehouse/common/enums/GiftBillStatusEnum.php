<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 仓储单据类型
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class GiftBillStatusEnum extends \common\enums\BaseEnum
{
    const GIFT_WAREHOUSE = 1;
    const GIFT_LOCK = 2;
    const GIFT_CANCE_LOCK = 3;
    const GIFT_OUT = 3;




    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::GIFT_WAREHOUSE   => '入库',
            self::GIFT_LOCK   => '锁定',
            self::GIFT_CANCE_LOCK   => '取消锁定',
            self::GIFT_OUT   => '出库',

        ];
    }

}