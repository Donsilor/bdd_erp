<?php

namespace addons\Style\common\enums;

/**
 * 款式材质 枚举（编款用）
 * @package common\enums
 */
class StyleMaterialEnum extends BaseEnum
{
    const GOLD = 1;
    const SILVER = 2;
    const COPPER = 3;
    const ALLOY = 4; 
    const OTHER = 0;
    /**
     * @return array
     *
     */
    public static function getMap(): array
    {
        return [
                self::GOLD => "金",
                self::SILVER => "银",
                self::COPPER => "铜",
                self::ALLOY => "合金",
                self::OTHER => "其它",
        ];
    }
    /**
     * 映射材质
     * @param string $value
     * @return boolean|string
     */
    public static function mapValue($value)
    {
        $id = false;
        if(preg_match("/k|au|pt/is", $value)) {
            $id = self::GOLD;
        }else if(preg_match("/ag/is", $value)) {
            $id = self::SILVER;
        }else if(preg_match("/铜/is", $value)) {
            $id = self::COPPER;
        }else if(preg_match("/合金/is", $value)){
            $id = self::ALLOY;
        }else if($value != '') {
            $id = self::OTHER;
        }        
        return $id;
    }
}