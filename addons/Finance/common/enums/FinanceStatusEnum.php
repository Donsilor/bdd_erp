<?php

namespace addons\Finance\common\enums;

/**
 * 订单状态
 * @package common\enums
 */
class FinanceStatusEnum extends \common\enums\BaseEnum
{
    const SAVE = 0;
    const PENDING = 1;
    const CONFORMED = 2;
    const FINISH = 3;
    const CANCAEL = 4;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [     

               self::SAVE => "保存",
               self::PENDING => "待审核",
               self::CONFORMED => "待确认",
               self::FINISH => "已完成",
               self::CANCAEL => "已关闭",


        ];
    }
    
}