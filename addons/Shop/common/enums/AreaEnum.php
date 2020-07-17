<?php

namespace addons\Shop\common\enums;

use common\enums\BaseEnum;

/**
 * 地区枚举
 * AreaEnum
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class AreaEnum extends BaseEnum
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
    /**
     * 是否开启  地区加价率
     * @return int 1|0
     */
    public static function isMarkupRate()
    {
        return 1;
    }
    /**
     * 是否开启  地区广告位
     * @return number
     */
    public static function isAdvert()
    {
        return 1;
    }
    
}