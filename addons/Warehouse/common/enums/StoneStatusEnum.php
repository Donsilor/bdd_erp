<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 石料状态
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class StoneStatusEnum extends \common\enums\BaseEnum
{
    const IN_STOCK = 1;
    const IN_PANDIAN = 2;
    const CANCEL = 99;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::IN_STOCK => '库存',
                self::IN_PANDIAN => '盘点中',
                self::CANCEL => '作废',
        ];
    }


}