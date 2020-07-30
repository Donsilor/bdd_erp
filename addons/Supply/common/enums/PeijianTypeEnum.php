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
    
    /**
     *
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::None => "不需配件",
                self::PeiJian => "需配件",
        ];
    }
    /**
     * 配件状态
     * @return string[][]|number[][]
     */
    public static function getPeiliaoStatus($peijian_type)
    {
        $map = [
                self::None=>PeijianStatusEnum::NONE,
                self::PeiJian=>PeijianStatusEnum::PENDING,
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