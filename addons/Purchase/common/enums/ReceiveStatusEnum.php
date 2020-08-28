<?php

namespace addons\Purchase\common\enums;

/**
 *
 * 收货状态
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class ReceiveStatusEnum extends \common\enums\BaseEnum
{
    
    const SAVE     = 0;
    const IN_RECEIVE    = 1;
    const HAS_RECEIVE   = 2;
    
    /**
     * 状态
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::SAVE    => '未收货',
            self::IN_RECEIVE => '部分收货',
            self::HAS_RECEIVE => '已收货',
        ];
    }

}