<?php

namespace addons\Supply\common\enums;

use common\enums\BaseEnum;

/**
 * 货品类型  枚举
 * @package common\enums
 */
class SupplierStatusEnum extends BaseEnum
{

    const RESERVE  = 1;
    const COOPERATE  = 2;
    const BLACKLIST = 3;


    /**
     * 
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::RESERVE => "储备",
            self::COOPERATE => "合作中",
            self::BLACKLIST => "黑名单",
        ];
    }
    
}