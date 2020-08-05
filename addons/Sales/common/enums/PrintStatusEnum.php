<?php

namespace addons\Sales\common\enums;

/**
 * 打印状态
 * @package common\enums
 */
class PrintStatusEnum extends \common\enums\BaseEnum
{
    
    const NO_PRINT  = 0;
    const HAS_PRINT = 1;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::NO_PRINT => "未打印",
                self::HAS_PRINT => "已打印",
        ];
    }
    
}