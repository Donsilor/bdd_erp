<?php

namespace addons\Sales\common\enums;

/**
 * 配送范围
 * @package common\enums
 */
class DeliveryScopeEnum extends \common\enums\BaseEnum
{
    
    const CHINA  = 1;
    const GAT = 2;
    const FOREIGN = 3;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::CHINA => "大陆地区",
                self::GAT => "港澳台",
                self::FOREIGN => "国外",
        ];
    }
    
}