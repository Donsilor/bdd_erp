<?php

namespace addons\Finance\common\enums;

/**
 * 所属项目
 * @package common\enums
 */
class BudgetTypeEnum extends \common\enums\BaseEnum
{
    const BUDGET_WITHIN = 1;
    const BUDGET_OFF = 2;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::BUDGET_WITHIN => "预算内",
            self::BUDGET_OFF => "预算外",
        ];
    }
    
}