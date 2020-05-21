<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 订单类型
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class OrderTypeEnum extends \common\enums\BaseEnum
{
    const ORDER_L = 1;
    const ORDER_K = 2;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::ORDER_L => '收货单',
            self::ORDER_K => '客订单',
        ];
    }

}