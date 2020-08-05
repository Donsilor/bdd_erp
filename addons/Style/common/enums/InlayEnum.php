<?php

namespace addons\Style\common\enums;


/**
 * 是否镶嵌
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class InlayEnum extends \common\enums\BaseEnum
{
    const Yes = 1;
    const No = 0;
    
    
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::Yes => '镶嵌',
                self::No => '非镶嵌',
        ];
    }
    
}