<?php

namespace addons\Gdzb\common\enums;

use common\enums\BaseEnum;

/**
 * 配件类型举
 * @package common\enums
 */
class TypeEnum extends BaseEnum
{
    const Contract_Goods = 1;
    const Loan_Goods = 2;

    /**
     *
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::Contract_Goods => "约货",
                self::Loan_Goods => "借货",

        ];
    }
    
}