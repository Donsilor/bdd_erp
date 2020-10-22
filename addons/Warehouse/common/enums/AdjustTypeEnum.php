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
    const ADD   = 1;
    const MINUS = 2;
    const RESTORE = 3;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::ADD   => '增加',
            self::MINUS => '减扣',
            self::MINUS => '还原',
        ];
    }

}