<?php

namespace addons\Supply\common\enums;

use common\enums\BaseEnum;

/**
 * 配件类型举
 * @package common\enums
 */
class PeijianTypeEnum extends BaseEnum
{
    const None = 1;
    const PeiJian = 2;
    const TwoPeiJian = 3;
    const ThreePeiJian = 4;
    
    /**
     *
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::None => "不需配件",
                self::PeiJian => "需一种配件",
                self::TwoPeiJian => "需二种配件",
                self::ThreePeiJian => "需三种配件",
        ];
    }
    /**
     * 配件状态
     * @return string[][]|number[][]
     */
    public static function getPeijianStatus($peijian_type)
    {
        $map = [
                self::None=>PeijianStatusEnum::NONE,
                self::PeiJian=>PeijianStatusEnum::PENDING,
                self::TwoPeiJian=>PeijianStatusEnum::PENDING,
                self::ThreePeiJian=>PeijianStatusEnum::PENDING,
        ];
        return $map[$peijian_type] ?? PeijianStatusEnum::NONE;
    }
    /**
     * 是否配件
     * @param int $peijian_type
     * @return boolean
     */
    public static function isPeijian($peijian_type)
    {
        return $peijian_type != self::None;
    }
    
}