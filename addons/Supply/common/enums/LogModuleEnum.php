<?php

namespace addons\Supply\common\enums;

use common\enums\BaseEnum;

/**
 * 货品类型  枚举
 * @package common\enums
 */
class LogModuleEnum extends BaseEnum
{
    const TO_FACTORY = 1;
    const TO_CONFIRMED  = 2;
    const TO_PRODUCE  = 3;
    const LEAVE_FACTORY  = 4;
    const QC_QUALITY  = 5;

    /**
     * 
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::TO_FACTORY => "分配工厂",
            self::TO_CONFIRMED => "确认分配",
            self::TO_PRODUCE => "开始生产",
            self::LEAVE_FACTORY => "生产出厂",
            self::QC_QUALITY => "QC质检",

        ];
    }
    
}