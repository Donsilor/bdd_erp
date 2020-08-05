<?php

namespace addons\Shop\common\enums;

use common\enums\BaseEnum;

/**
 * 同步平台枚举
 * AreaEnum
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class SyncPlatformEnum extends BaseEnum
{
    const SYNC_EPR = 1;
    
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::SYNC_EPR => '同步ERP',                
        ];
    }

}