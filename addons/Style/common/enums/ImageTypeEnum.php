<?php

namespace addons\Style\common\enums;

/**
 * 表面工艺  枚举
 * @package common\enums
 */
class ImageTypeEnum extends BaseEnum
{
    const ORIGINAL = 1;
    const Glaze = 2;
    const Frosted = 3;
    const WireDrawing = 4;
    const GlazeFrosted = 5;
    /**
     * @return array
     *光面，磨砂，拉丝，光面+磨砂
     */
    public static function getMap(): array
    {
        return [
                self::ORIGINAL => "商品图",
                self::Glaze => "光面",
                self::Frosted => "磨砂",
                self::WireDrawing => "拉丝",
                self::GlazeFrosted => "光面&磨砂",
        ];
    }
    
}