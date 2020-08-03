<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 样板状态
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class TempletStatusEnum extends \common\enums\BaseEnum
{
    const IN_STOCK = 1;
    const SOLD_OUT = 2;
    const CANCEL = 99;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::IN_STOCK => '库存',
                self::SOLD_OUT => '已售馨',
                self::CANCEL => '作废',
        ];
    }


}