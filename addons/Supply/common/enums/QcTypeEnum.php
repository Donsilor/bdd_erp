<?php

namespace addons\Supply\common\enums;

use common\enums\BaseEnum;

/**
 * 质检  枚举
 * @package common\enums
 */
class QcTypeEnum extends BaseEnum
{
    const PASS = 1;
    const NOT_PASS = 0;
    
    /**
     * 
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::PASS => "质检通过",
            self::NOT_PASS => "质检未过",
            
        ];
    }
    
}