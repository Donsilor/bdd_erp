<?php

namespace addons\Purchase\common\enums;

/**
 * 特殊工艺
 * @package common\enums
 */
class SpecialCraftEnum extends BaseEnum
{

    const Glaze = 1;
    const Frosted = 2;
    const WireDrawing = 3;
    const GlazeFrosted = 4;
    const HammerPattern = 5;
    const VintageBrushed = 6;
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
            self::HammerPattern => "锤纹",
            self::VintageBrushed => "复古拉丝",
        ];
    }
    
}