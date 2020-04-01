<?php

namespace common\enums;

/**
 * 加工费类型 枚举
 * @package common\enums
 */
class FactoryFeeEnum extends BaseEnum
{
    const FEE_18K = 1;
    const FEE_PT950 = 2;
    const FEE_SILVER = 3;
    const FEE_CHAOSHI = 4;
    const FEE_CHAOZHONG = 5;
    const FEE_SURFACE = 6;
    /**
     * @return array
     * 
     */
    public static function getMap(): array
    {
        return [
                self::FEE_18K => "18K工费",
                self::FEE_PT950 => "PT950工费",
                self::FEE_SILVER => "银工费",
                self::FEE_CHAOSHI => "超石工费",
                self::FEE_CHAOZHONG => "超重工费",
                self::FEE_SURFACE => "表面工艺",
                self::FEE_FINGER => "改圈工费",
        ];
    }   
    
}