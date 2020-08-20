<?php

namespace addons\Sales\common\enums;

/**
 * 退款单确认状态
 * @package common\enums
 */
class CheckStatusEnum extends \common\enums\BaseEnum
{
    const SAVE = 0;
    const LEADER = 1;
    const STOREKEEPER = 2;
    const FINANCE = 3;
    const DONE = 10;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::SAVE => "未操作",
            self::LEADER => "主管确认通过",
            self::STOREKEEPER => "库管确认通过",
            self::FINANCE => "财务确认通过",
            self::DONE => '退款完成',
        ];
    }

}