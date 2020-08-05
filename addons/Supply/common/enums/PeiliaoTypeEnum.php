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
    const PeiLiao = 2;  
    
    /**
     *
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::None => "不需配金料",
                self::PeiLiao => "需配金料",                
        ];
    }
    /**
     * 配石/配料状态
     * @return string[][]|number[][]
     */
    public static function getPeiliaoStatus($peiliao_type)
    {
        $map = [
                self::None=>PeiliaoStatusEnum::NONE,
                self::PeiLiao=>PeiliaoStatusEnum::PENDING,
        ];
        return $map[$peiliao_type] ?? PeiliaoStatusEnum::NONE;
    }
    /**
     * 是否配料
     * @param unknown $type
     * @return boolean
     */
    public static function isPeiliao($peiliao_type)
    {
        return $peiliao_type != self::None;
    }
    
}