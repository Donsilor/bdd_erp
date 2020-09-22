<?php

namespace addons\Gdzb\common\enums;

use common\enums\BaseEnum;

/**
 * 配件类型举
 * @package common\enums
 */
class OrderFromEnum extends BaseEnum
{
    const FROM_HAND = 1;
    const INTER_SYNCH = 2;
    const ORDER_IMPORT = 3;



    /**
     *
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::FROM_HAND => "手动创建",
                self::INTER_SYNCH => "接口同步",
                self::ORDER_IMPORT => "订单导入",
        ];
    }

}