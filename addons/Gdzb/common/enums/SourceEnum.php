<?php

namespace addons\Gdzb\common\enums;

use common\enums\BaseEnum;

/**
 * 配件类型举
 * @package common\enums
 */
class SourceEnum extends BaseEnum
{
    const General = 1;
    const Market_Find = 2;
    const Friend_Introduce = 3;
    const Other = 4;


    /**
     *
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::General => "公司资源",
                self::Market_Find => "市场自找",
                self::Friend_Introduce => "熟人介绍",
                self::Other => "其它",
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