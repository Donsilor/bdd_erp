<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 仓库类型
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseTypeEnum extends \common\enums\BaseEnum
{
    const ON_THE_WAY = 1;
    const REPAIR = 2;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::ON_THE_WAY => '良品',
            self::ON_THE_WAY => '在途',
            self::REPAIR => '维修',
        ];
    }
    /**
     * 空托类型与属性类型关系映射
     * @return array
     */
    public static function getAttrTypeMap(): array
    {
        return [
            self::Chengpin => [
                AttrTypeEnum::TYPE_BASE,
                AttrTypeEnum::TYPE_COMBINE,
                AttrTypeEnum::TYPE_SALE
            ],
            self::Kongtuo => [
                AttrTypeEnum::TYPE_BASE,
                AttrTypeEnum::TYPE_SALE
            ],
        ];
    }

}