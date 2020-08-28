<?php

namespace addons\Sales\common\enums;

/**
 * 支付方式
 * @package common\enums
 */
class ReturnTypeEnum extends \common\enums\BaseEnum
{
    const CARD = 1;
    const TRANSFER = 2;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::CARD => '退款',
            self::TRANSFER => '转单'
        ];
    }

}