<?php

namespace addons\Style\common\enums;


/**
 * 是否镶嵌
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class IsApply extends \common\enums\BaseEnum
{
    const Wait = 2;
    const Yes = 1;
    const No = 0;
    
    
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::Yes => '采购申请（已审核）',
                self::Wait => '采购申请（待审核）',
                self::No => '非采购申请',
        ];
    }
    
}