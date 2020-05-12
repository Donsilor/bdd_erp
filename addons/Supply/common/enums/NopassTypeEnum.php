<?php

namespace addons\Supply\common\enums;

use common\enums\BaseEnum;

/**
 * 质检未过类型 枚举
 * @package common\enums
 */
class NopassTypeEnum extends BaseEnum
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
            self::NO_PASS_ONE => "类型1",
            self::NO_PASS_TWO => "类型2",
            self::NO_PASS_THREE => "类型3",
            self::NO_PASS_FOUR => "类型4",
            self::NO_PASS_FIVES => "类型5",

        ];
    }
    
}