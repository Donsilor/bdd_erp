<?php

namespace addons\Style\common\enums;


/**
 * 金托类型枚举
 * 分类类型(1-基础属性,2-销售属性,3-定制属性,4款式分类)
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class JintuoTypeEnum extends \common\enums\BaseEnum
{
    const Chengpin = 1;
    const Kongtuo = 2;    
    
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::Chengpin => '成品',
                self::Kongtuo => '空托',
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

    /**
     * @param string $name
     * @return int
     */
    public static function getIdByName($name)
    {
        $data = array_flip(JintuoTypeEnum::getMap());
        return $data[$name] ?? "";
    }
}