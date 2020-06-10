<?php

namespace addons\Purchase\common\enums;

/**
 * 采购单类型
 * @package common\enums
 */
class OrderTypeEnum extends BaseEnum
{
    
    const SILVER_PLATE = 1;
    const NORMAL = 2;
    const CUSTOMIZED = 3;
    const TRIAL_EDITION = 4;
    /**
     *
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::SILVER_PLATE => "银版",
                self::NORMAL =>'常规',
                self::CUSTOMIZED =>'定制',
                self::TRIAL_EDITION =>'定制',
        ];
    }
    
}