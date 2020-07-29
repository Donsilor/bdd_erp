<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 配件状态
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class PartsStatusEnum extends \common\enums\BaseEnum
{
    const IN_STOCK = 1;
    const IN_PANDIAN = 2;
    const SOLD_OUT = 3;
    const CANCEL = 99;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::IN_STOCK => '库存',
                self::IN_PANDIAN => '盘点中',
                self::SOLD_OUT => '已售馨',
                self::CANCEL => '作废',
        ];
    }


}