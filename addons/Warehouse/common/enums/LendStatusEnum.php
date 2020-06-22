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
    const LEND = 2;
    const RETURN = 3;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::LEND => '借货',
            self::RETURN => '还货',
        ];
    }

}