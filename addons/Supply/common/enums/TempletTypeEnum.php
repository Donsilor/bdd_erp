<?php

namespace addons\Supply\common\enums;

use common\enums\BaseEnum;

/**
 * 样板类型举
 * @package common\enums
 */
class TempletTypeEnum extends BaseEnum
{
    const NONE = 1;
    const SILVER = 2;
    const RUBBER = 3;
    const SILVER_RUBBER = 9;

    /**
     *
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::NONE => "不需出样板",
            self::SILVER => "需出银版",
            self::RUBBER => "需出胶膜版",
            self::SILVER_RUBBER => "需出银版及胶模版",
        ];
    }

}