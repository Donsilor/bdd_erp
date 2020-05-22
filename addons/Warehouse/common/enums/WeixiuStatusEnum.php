<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 维修状态
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class WeixiuStatusEnum extends \common\enums\BaseEnum
{
    const SAVE = 0;
    const APPLY = 1;
    const ACCEPT = 2;
    const FINISH = 3;
    const WAIT_SHIP = 4;
    const IN_TRANSFER = 5;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::SAVE => '保存',
            self::APPLY => '维修申请',
            self::ACCEPT => '维修受理',
            self::FINISH => '维修完成',
            self::WAIT_SHIP => '待发货',
            self::IN_TRANSFER => '转仓中',
        ];
    }

}