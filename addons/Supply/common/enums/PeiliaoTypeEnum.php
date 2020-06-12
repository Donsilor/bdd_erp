<?php

namespace addons\Supply\common\enums;

use common\enums\BaseEnum;

/**
 * 配料类型举
 * @package common\enums
 */
class PeiliaoTypeEnum extends BaseEnum
{
    const None = 1;
    const PeiShi = 2;
    const PeiLiao = 3;
    const All = 4;  
    
    /**
     *
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::None => "不需配石配料",
                self::PeiShi => "需配石",
                self::PeiLiao => "需配料",
                self::All => "需配石配料",                
        ];
    }
    
}