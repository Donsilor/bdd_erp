<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 仓库类型
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class WeixiuStatusEnum extends \common\enums\BaseEnum
{
    const WX_MIAN = 1;
    const WX_APPLY = 2;
    const WX_ACCEPT = 3;
    const WX_FINISH = 4;
    const BE_SHIP = 5;
    const IN_WAREHOUSE = 6;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::WX_MIAN => '维修CANCEL',
            self::WX_APPLY => '维修申请',
            self::WX_ACCEPT => '维修受理',
            self::WX_FINISH => '维修完成',
            self::BE_SHIP => '待发货',
            self::IN_WAREHOUSE => '转仓中',


        ];
    }

}