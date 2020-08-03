<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 样板单据类型
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class TempletBillTypeEnum extends \common\enums\BaseEnum
{
    const GOLD_L   = 'YL';
    const GOLD_C   = 'YC';

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::GOLD_L   => '入库单',
            self::GOLD_C   => '出库单',
        ];
    }

}