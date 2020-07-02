<?php

namespace common\enums;

/**
 * 流程方式
 *
 * Class StatusEnum
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class FlowMethodEnum extends BaseEnum
{
    const IN_ORDER = 1;//按顺序
    const NO_ORDER = 2;//不按顺序

    
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::IN_ORDER => "按顺序",
                self::NO_ORDER => "不按顺序",

        ];
    }   

    
}