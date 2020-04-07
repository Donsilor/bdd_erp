<?php

namespace addons\Style\common\enums;

/**
 * 表面工艺  枚举
 * @package common\enums
 */
class FaceWorkEnum extends BaseEnum
{
    const Glaze = 1;
    const Frosted = 2;
    const WireDrawing = 3;
    const GlazeFrosted = 4;
    /**
     * @return array
     *光面，磨砂，拉丝，光面+磨砂
     */
    public static function getMap(): array
    {
        return [
                self::Glaze => "光面",
                self::Frosted => "磨砂",
                self::WireDrawing => "拉丝",
                self::GlazeFrosted => "光面&磨砂",
        ];
    }
    
}