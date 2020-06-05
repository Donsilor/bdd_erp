<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 石包调整类型
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class AdjustTypeEnum extends \common\enums\BaseEnum
{
    const MINUS = 0;
    const ADD   = 1;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::MINUS => '减扣',
            self::ADD   => '增加',
        ];
    }

}