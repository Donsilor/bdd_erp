<?php

namespace common\enums;

/**
 * 状态枚举
 *
 * Class StatusEnum
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class StatusEnum extends BaseEnum
{
    const ENABLED = 1;
    const DISABLED = 0;    
    const LOCKED = 2;
    const DELETE = -1;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::ENABLED => '启用',
            self::DISABLED => '禁用',
        ];
    }
    
    /**
     * 锁定状态列表
     * @return array
     */
    public static function getLockMap(): array
    {
        return [
                self::ENABLED => '启用',
                self::DISABLED => '禁用',
                self::LOCKED => '锁定',
                // self::DELETE => '已删除',
        ];
    }
}