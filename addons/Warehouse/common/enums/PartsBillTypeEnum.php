<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 配件单据类型
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class PartsBillTypeEnum extends \common\enums\BaseEnum
{
    const PARTS_L   = 'PL';
    const PARTS_C   = 'PC';
    const PARTS_D   = 'PD';
    const PARTS_W   = 'PW';

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::PARTS_L   => '入库单',
            self::PARTS_C   => '领件单',
            self::PARTS_D   => '退件单',
            self::PARTS_W   => '盘点单',
        ];
    }

}