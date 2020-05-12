<?php

namespace addons\Supply\common\enums;

use common\enums\BaseEnum;

/**
 * 质检未过类型 枚举
 * @package common\enums
 */
class NopassReasonEnum extends BaseEnum
{
    const NO_PASS_ONE = 1;
    const NO_PASS_TWO  = 2;
    const NO_PASS_THREE  = 3;
    const NO_PASS_FOUR  = 4;
    const NO_PASS_FIVES  = 5;

    /**
     * 
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::NO_PASS_ONE => "原因1",
            self::NO_PASS_TWO => "原因2",
            self::NO_PASS_THREE => "原因3",
            self::NO_PASS_FOUR => "原因4",
            self::NO_PASS_FIVES => "原因5",

        ];
    }
    
}