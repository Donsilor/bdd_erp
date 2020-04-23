<?php

namespace addons\Purchase\common\enums;

use common\enums\BaseEnum;

/**
 * 表面工艺  枚举
 * @package common\enums
 */
class PurchaseGoodsTypeEnum extends BaseEnum
{
    const STYLE = 1;
    const QIBAN = 2;

    
    /**
     *
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::STYLE => "款式商品",
                self::QIBAN => "起版商品",                
        ];
    }
    
}