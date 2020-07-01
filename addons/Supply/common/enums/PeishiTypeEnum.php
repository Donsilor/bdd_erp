<?php

namespace addons\Supply\common\enums;

use common\enums\BaseEnum;

/**
 * 配石类型举
 * @package common\enums
 */
class PeishiTypeEnum extends BaseEnum
{
    const None = 1;
    const MAIN_STONE = 2;
    const SIDE_STONE = 3;
    const All = 4;
    
    /**
     *
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::None => "不需配石",
                self::MAIN_STONE => "需配主石",
                self::SIDE_STONE => "需配副石",
                self::All => "需配主石副石",
        ];
    }
    /**
     * 配石状态
     * @param unknown $peishi_type
     * @return number|string
     */
    public static function getPeishiStatus($peishi_type)
    {
        $map = [
                self::None=>PeishiStatusEnum::NONE,
                self::MAIN_STONE=>PeishiStatusEnum::PENDING,
                self::SIDE_STONE=>PeiliaoStatusEnum::PENDING,
                self::All=>PeishiStatusEnum::PENDING,
        ];
        return $map[$peishi_type] ?? PeishiStatusEnum::NONE;
    }
    /**
     * 是否配料
     * @param unknown $type
     * @return boolean
     */
    public static function isPeishi($peishi_type)
    {
        return $peishi_type != self::None;
    }
    
}