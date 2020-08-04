<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 借货状态
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class LendStatusEnum extends \common\enums\BaseEnum
{
    const SAVE = 0;
    const IN_RECEIVE = 1;
    const HAS_LEND = 2;
    const HAS_RETURN = 3;
    const PORTION_RETURN = 4;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::SAVE => '保存',
            self::IN_RECEIVE => '待接收',
            self::HAS_LEND => '已借货',
            self::HAS_RETURN  => '已还货',
        ];
    }

    /**
     *
     * @return array
     */
    public static function getBillMap(): array
    {
        return [
            self::HAS_LEND => '已借货',
            self::PORTION_RETURN => '部分还货',
            self::HAS_RETURN => '已还货',
        ];
    }

}