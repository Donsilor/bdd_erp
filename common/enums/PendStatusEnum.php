<?php

namespace common\enums;

/**
 * 处理状态
 *
 * Class StatusEnum
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class PendStatusEnum extends BaseEnum
{
    const PENDING = 0;
    const CONFIRM = 1;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::PENDING => "待处理",
            self::CONFIRM => "已处理",
        ];
    }


}