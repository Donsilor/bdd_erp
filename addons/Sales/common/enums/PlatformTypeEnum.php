<?php

namespace addons\Sales\common\enums;

use common\enums\BaseEnum;

/**
 * 平台类型
 * @package common\enums
 */
class PlatformTypeEnum extends BaseEnum
{    
    const PLATRORM = 1;
    const CUSTOMER = 2;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::PLATRORM =>'平台收货代发',
                self::CUSTOMER =>'直接发给客户'
        ];
    }
    
}