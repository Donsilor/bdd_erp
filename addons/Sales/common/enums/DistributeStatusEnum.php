<?php

namespace addons\Sales\common\enums;

/**
 * 配货状态
 * @package common\enums
 */
class DistributeStatusEnum extends \common\enums\BaseEnum
{
    
    const SAVE = 0;
    const ALLOWED = 1;
    const IN_PEIHUO = 2;
    const HAS_PEIHUO = 3;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::SAVE => "未配货",
                self::ALLOWED => "允许配货",
                self::IN_PEIHUO => "配货中",
                self::HAS_PEIHUO => "已配货",
        ];
    }
    
}