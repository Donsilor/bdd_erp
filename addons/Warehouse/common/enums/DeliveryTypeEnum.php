<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 出库类型
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class DeliveryTypeEnum extends \common\enums\BaseEnum
{
    const QUICK_SALE = 1;
    const PROXY_PRODUCE = 2;
    const PART_GOODS = 3;
    const ASSEMBLY = 4;
    const PLATFORM = 5;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::QUICK_SALE => '订单快捷出库',
            //self::PROXY_PRODUCE => '委托加工',
            //self::PART_GOODS => '拆货',
            //self::ASSEMBLY => '货品组装',
            self::PLATFORM => '平台出库',
        ];
    }

}