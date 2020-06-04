<?php

namespace common\enums;

/**
 * 编码方式
 *
 * Class AutoSnEnum
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class AutoSnEnum extends BaseEnum
{
    const YES = 1;
    const NO = 0;
    
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::YES => '系统生成',
                self::NO  => '手动编辑',
        ];
    }
}