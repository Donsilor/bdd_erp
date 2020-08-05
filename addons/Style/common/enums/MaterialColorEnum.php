<?php

namespace addons\Style\common\enums;

/**
 * 材质颜色 枚举
 * @package common\enums
 */
class MaterialColorEnum extends BaseEnum
{
    const WHITE = 'W';
    const YELLOW = 'Y';
    const ROSE_YELLOW = 'RY';
    const ROSE_WHITE = 'RW';
    const YELLOW_WHITE = 'YW';
    
    /**
     * 材质颜色
     * @return array
     *
     */
    public static function getMap(): array
    {
        return [
                self::WHITE => "白",
                self::YELLOW => "黄",
                self::ROSE_GOLD => "玫瑰黄",
                self::ROSE_WHITE => "玫瑰白",
                self::YELLOW_WHITE => "黄白",                
        ];
    }
    
    
    
}