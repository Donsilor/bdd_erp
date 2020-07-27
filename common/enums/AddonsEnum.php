<?php
namespace common\enums;


/**
 * 模块枚举
 *
 * Class AddonsEnum
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class AddonsEnum extends BaseEnum
{
    const STYLE = 'style';//款式管理
    const PURCHASE = 'purchase';//采购管理
    const SUPPLY = 'supply';//生产管理
    const SALES = 'sales';//销售管理
    const BACNEND = 'backend';//后台管理
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::STYLE => "款式管理",
                self::PURCHASE => "采购管理",
                self::SUPPLY => "生产管理",
                self::SALES => "销售管理",
                self::BACNEND => "后台管理",
        ];
    }
    
}