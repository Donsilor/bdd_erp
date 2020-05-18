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
    const PENDING = 0;
    const PASS = 1;    
    const UNPASS = 2;
	const CLOSE = 3;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::PENDING => '待审核',
                self::PASS => '审核通过',                
                self::UNPASS => '不通过',
                //self::CLOSE => '已关闭',
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