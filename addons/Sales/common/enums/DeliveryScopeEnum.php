<?php

namespace addons\Sales\common\enums;

/**
 * 配送范围
 * @package common\enums
 */
class DeliveryScopeEnum extends \common\enums\BaseEnum
{
    const China = 1;
    const HongKong = 2;
    const MaCao = 3;
    const TaiWan = 4;
    const Other = 99;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::China => '中国',
            self::HongKong => '香港',
            self::MaCao => '澳门',
            self::TaiWan => '台湾',
            self::Other => '国外',
        ];
    }
    
}