<?php

namespace addons\Style\common\enums;


/**
 * 属性类型枚举
 * 分类类型(1-基础属性,2-销售属性,3-定制属性,4款式分类)
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class CombineEnum extends \common\enums\BaseEnum
{
    const Combine = 1;
    const UnCombine = 0;
    
    
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::Combine => '镶嵌',
                self::UnCombine => '非镶嵌',
        ];
    }
    
}