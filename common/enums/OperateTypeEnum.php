<?php

namespace common\enums;

/**
 * 操作方式  1.人工 2系统
 *
 * Class OperateTypeEnum
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class OperateTypeEnum extends BaseEnum
{
    const USER = 1;
    const SYSTEM = 2;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::USER => '人工操作',
                self::SYSTEM => '系统同步',
        ];
    }
    
}