<?php

namespace addons\Warehouse\common\enums;

/**
 *
 * 配件方式
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class PeiJianCateEnum extends \common\enums\BaseEnum
{
    const BUCKLE = 1;
    const BACK_EAR = 2;
    const BUTTON = 3;
    const CHAIN = 4;
    const EAR_STICK = 5;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::BUCKLE => '扣环',
            self::BACK_EAR => '耳背',
            self::BUTTON => '扣子',
            self::CHAIN => '链子',
            self::EAR_STICK => '耳棒',
        ];
    }

}