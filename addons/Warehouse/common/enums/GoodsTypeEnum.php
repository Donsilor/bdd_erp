<?php

namespace addons\Warehouse\common\enums;

/**
 *
 * 商品类型
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class GoodsTypeEnum extends \common\enums\BaseEnum
{
    const All = 0;
    const PlainGold = 1;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::All => '金工石',
            self::PlainGold => '素金',
        ];
    }

}