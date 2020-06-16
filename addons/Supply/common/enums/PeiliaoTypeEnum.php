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
    const PeiShi = 2;
    const PeiLiao = 3;
    const All = 4;  
    
    /**
     *
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::None => "不需配石配料",
                self::PeiShi => "需配石",
                self::PeiLiao => "需配料",
                self::All => "需配石配料",                
        ];
    }
    /**
     * 配石/配料状态
     * @return string[][]|number[][]
     */
    public static function getStatusMap()
    {
        return [
                self::None=>[PeishiStatusEnum::NONE,PeiliaoStatusEnum::NONE],
                self::PeiShi=>[PeishiStatusEnum::PENDING,PeiliaoStatusEnum::NONE],
                self::PeiLiao=>[PeishiStatusEnum::NONE,PeiliaoStatusEnum::PENDING],
                self::All=>[PeishiStatusEnum::PENDING,PeiliaoStatusEnum::PENDING],
        ];
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