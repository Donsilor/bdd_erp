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
    const SeikoStone = 1;
    const PlainGold = 2;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::SeikoStone => '精工石',
            self::PlainGold => '素金',
        ];
    }

}