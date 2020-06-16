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
    const OTHER = 2;
    /**
     *
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::STYLE => "有款商品",
                self::OTHER => "无款商品",
        ];
    }
    
}