<?php

namespace addons\Style\common\enums;

/**
 * 表面工艺  枚举
 * @package common\enums
 */
class ImagePositionEnum extends BaseEnum
{
    const POSITIVE = 1;
    const SIDE = 2;
    const DEGREES_45 = 3;

    /**
     * @return array
     *光面，磨砂，拉丝，光面+磨砂
     */
    public static function getMap(): array
    {
        return [
                self::POSITIVE => "正面",
                self::SIDE => "侧面",
                self::DEGREES_45 => "45度",
        ];
    }
    
}