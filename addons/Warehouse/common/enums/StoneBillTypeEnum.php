<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 石包单据类型
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class StoneBillTypeEnum extends \common\enums\BaseEnum
{
    const STONE_MS   = 'MS';
    const STONE_SS   = 'SS';
    const STONE_HS   = 'HS';
    const STONE_TS   = 'TS';
    const STONE_YS   = 'YS';
    const STONE_SY   = 'SY';
    const STONE_RK   = 'RK';
    const STONE_CK   = 'CK';
    const STONE_W    = 'SW';

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::STONE_MS   => '入库单',
            self::STONE_SS   => '领石单',
            self::STONE_HS   => '还石单',
            self::STONE_TS   => '返厂单',
            self::STONE_YS   => '遗失单',
            self::STONE_SY   => '损益单',
            self::STONE_RK   => '其它入库单',
            self::STONE_CK   => '其它出库单',
            self::STONE_W    => '盘点单',
        ];
    }

}