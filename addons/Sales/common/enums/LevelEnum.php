<?php

namespace addons\Sales\common\enums;

/**
 * 客户等级
 * @package common\enums
 */
class LevelEnum extends \common\enums\BaseEnum
{
    
    const GENERAL = 1;
    const VIP = 2;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::GENERAL => '普通客户',
                self::VIP => 'VIP客户',
        ];
    }
    
}