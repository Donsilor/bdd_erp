<?php

namespace addons\Sales\common\enums;

/**
 * 退款方式
 * @package common\enums
 */
class ReturnByEnum extends \common\enums\BaseEnum
{
    const GOODS = 1;
    const NO_GOODS = 2;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::GOODS => '退商品',
            self::NO_GOODS => '不退商品'
        ];
    }

}