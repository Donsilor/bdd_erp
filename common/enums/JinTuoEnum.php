<?php

namespace common\enums;

/**
 * 审核状态枚举
 *
 * Class AuditStatusEnum
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class JinTuoEnum extends BaseEnum
{
    const MOUNTINGS = 1;
    const FINISHED = 2;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::MOUNTINGS => '空托',
                self::FINISHED => '成品',

        ];
    }
}