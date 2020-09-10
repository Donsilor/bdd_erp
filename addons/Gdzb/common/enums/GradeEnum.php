<?php

namespace addons\Gdzb\common\enums;

use common\enums\BaseEnum;

/**
 * 配件类型举
 * @package common\enums
 */
class GradeEnum extends BaseEnum
{
    const General = 0;
    const One = 1;
    const Two = 2;
    const Three = 3;
    const Four = 4;
    const Five = 5;

    /**
     *
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::General => "普通",
                self::One => "1级",
                self::Two => "2级",
                self::Three => "3级",
                self::Four => "4级",
                self::Five => "5级",
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