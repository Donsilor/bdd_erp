<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 质检状态
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class QcStatusEnum extends \common\enums\BaseEnum
{
    
    const SAVE     = 0;
    const PASS    = 1;
    const NOT_PASS   = 2;
    
    /**
     * 单据通用状态
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::SAVE      => '未质检',
            self::PASS      => '质检通过',
            self::NOT_PASS  => '质检未过',
        ];
    }

}