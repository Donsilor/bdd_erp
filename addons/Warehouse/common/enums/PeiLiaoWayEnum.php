<?php

namespace addons\Warehouse\common\enums;

/**
 *
 * 配石类型
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class PeiLiaoWayEnum extends \common\enums\BaseEnum
{
    const NO_PEI = 0;
    const COMPANY = 1;
    const FACTORY = 2;
    const LAILIAO = 3;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::NO_PEI => '不需配料',
            self::COMPANY => '公司配',
            self::FACTORY => '工厂配',
            self::LAILIAO=> '来料加工',
        ];
    }

    /**
     * @param string $name
     * @return int
     */
    public static function getIdByName($name)
    {
        $data = array_flip(PeiLiaoWayEnum::getMap());
        return $data[$name] ?? "";
    }

}