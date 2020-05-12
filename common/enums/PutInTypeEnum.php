<?php

namespace common\enums;

/**
 * 采购类型  枚举
 * @package common\enums
 */
class PutInTypeEnum extends BaseEnum
{
    const Buy = 1;
    const W = 2;
    const SYSTEM = 3;
    
    /**
     *
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::WAIT_AUDIT => "待审核",
                self::DATA_SYNCH => "已审核",
                self::SYSTEM => "不通过",
                
        ];
    }
    
}