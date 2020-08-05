<?php

namespace addons\Style\common\enums;

/**
 * 属性模块枚举
 * Class AttrModuleEnum
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class AttrModuleEnum extends \common\enums\BaseEnum
{
    const STYLE = 1;
    const QIBAN = 2;
    const PURCHASE = 3;
    const SALE = 4;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::STYLE => '款式模块',
                self::QIBAN => '起版模块',
                self::PURCHASE => '采购模块',
                self::SALE => '销售模块',
        ];
    }
    
}