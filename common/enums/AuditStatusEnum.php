<?php

namespace common\enums;

/**
 * 审核状态枚举
 *
 * Class AuditStatusEnum
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class AuditStatusEnum extends BaseEnum
{
    const SAVE = 0;
    const PENDING = 1;
    const PASS = 2;
    const UNPASS = 3;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::SAVE => '未审核',
                self::PENDING => '待审核',
                self::PASS => '审核通过',
                self::UNPASS => '不通过',
        ];
    }
    /**
     * 
     * @return array
     */
    public static function getAuditMap(): array
    {
        return [
                self::PASS => '通过',
                self::UNPASS => '不通过',
        ];
    }
}