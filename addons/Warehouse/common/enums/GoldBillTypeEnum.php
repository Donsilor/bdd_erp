<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 金料单据类型
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class GoldBillTypeEnum extends \common\enums\BaseEnum
{
    const GOLD_L   = 'GL';

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::GOLD_L   => '入库单',
        ];
    }

}