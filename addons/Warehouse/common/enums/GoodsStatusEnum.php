<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 仓库类型
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class GoodsStatusEnum extends \common\enums\BaseEnum
{
    const IN_STOCK = 1;
    const IN_SALE = 2;
    const HAS_SOLD = 3;
    const IN_PANDIAN = 4;
    const IN_TRANSFER = 5;
    const IN_RETURN_FACTORY = 6;
    const HAS_RETURN_FACTORY = 7;
    const IN_REFUND = 8;
    const CANCEL = 99;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::IN_STOCK => '库存',
                self::IN_SALE => '销售中',
                self::HAS_SOLD => '已销售',
                self::IN_PANDIAN => '盘点中',
                self::IN_TRANSFER => '调拨中',
                self::IN_RETURN_FACTORY => '返厂中',
                self::HAS_RETURN_FACTORY => '已返厂',
                self::IN_REFUND => '退货中',
                self::CANCEL => '作废',
        ];
    }


}