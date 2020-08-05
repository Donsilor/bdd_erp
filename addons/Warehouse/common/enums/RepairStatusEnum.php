<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 维修单据状态
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class RepairStatusEnum extends \common\enums\BaseEnum
{
    const SAVE      = 0;
    const APPLY     = 1;
    const FINISHED    = 2;
    const AWAIT     = 3;
    const ORDERS    = 4;
    const FINISH    = 5;
    const RECEIVING = 6;
    const CANCEL    = 10;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::SAVE      => '保存',
            self::APPLY     => '申请',
            self::FINISHED    => '确认',
            self::AWAIT     => '等待',
            self::ORDERS    => '下单',
            self::FINISH    => '完毕',
            self::RECEIVING => '收货',
            self::CANCEL    => '取消',
        ];
    }

}