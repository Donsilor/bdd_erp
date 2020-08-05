<?php

namespace addons\Style\common\enums;

/**
 * 材质 枚举
 * @package common\enums
 */
class MaterialTypeEnum extends BaseEnum
{
    const CODE_18K = '18K';
    const CODE_PT = 'PT';
    const CODE_GOLD = 'GOLD';
    const CODE_SILVER = 'SILVER';
    const CODE_ALLOY = 'ALLOY';
    
    const ID_18K = 1;
    const ID_PT = 2;
    const ID_GOLD = 3;
    const ID_SILVER = 4;
    const ID_ALLOY = 5;
    /**
     * 
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::ID_18K => "18K",
                self::ID_PT => "铂金",
                self::ID_GOLD => "黄金",
                self::ID_SILVER => "银",
                self::ID_ALLOY => "合金 ",
                
        ];
    }
    /**
     * 
     * @return string[]
     */
    public static function getCodeMap()
    {
        return [
                self::CODE_18K => "18K",
                self::CODE_PT => "铂金",
                self::CODE_GOLD => "黄金",
                self::CODE_SILVER => "银",
                self::CODE_ALLOY => "合金 ",
                
        ];
    }
    
}