<?php

namespace addons\Sales\common\enums;

/**
 * 退款单状态
 * @package common\enums
 */
class ReturnStatusEnum extends \common\enums\BaseEnum
{
    const SAVE = 0;
    const LEADER = 1;
    const STOREKEEPER = 2;
    const FINANCE = 3;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::SAVE => "保存",
            self::LEADER => "主管审核通过",
            self::STOREKEEPER => "库管审核通过",
            self::FINANCE => "财务审核通过",
        ];
    }

}