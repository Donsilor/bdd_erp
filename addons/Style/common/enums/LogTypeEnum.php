<?php

namespace addons\Style\common\enums;

/**
 * 日志类型
 * @package common\enums
 */
class LogTypeEnum extends BaseEnum
{
    const ARTIFICIAL = 1;
    const DATA_SYNCH = 2;
    const SYSTEM = 3;

    /**
     * @return array
     *光面，磨砂，拉丝，光面+磨砂
     */
    public static function getMap(): array
    {
        return [
                self::ARTIFICIAL => "人为操作",
                self::DATA_SYNCH => "数据同步",
                self::SYSTEM => "系统操作",

        ];
    }
    
}