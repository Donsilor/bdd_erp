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
    const GOLD_C   = 'GC';
    const GOLD_D   = 'GD';
    const GOLD_W   = 'GW';

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::GOLD_L   => '入库单',
            self::GOLD_C   => '领料单',
            self::GOLD_D   => '退料单',
            self::GOLD_W   => '盘点单',
        ];
    }

}