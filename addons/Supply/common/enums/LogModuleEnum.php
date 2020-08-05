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
    const TO_PEILIAO  = 3;
    const CONFIRM_PEILIAO  = 4;
    const TO_PRODUCE  = 5;
    const LEAVE_FACTORY  = 6;
    const QC_QUALITY  = 7;
    const SET_PEILIAO  = 8;

    /**
     * 
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::TO_FACTORY => "分配工厂",
            self::TO_CONFIRMED => "确认分配",
            self::TO_PEILIAO => "开始配料",
            self::CONFIRM_PEILIAO => "确认配料",
            self::TO_PRODUCE => "开始生产",
            self::LEAVE_FACTORY => "生产出厂",
            self::QC_QUALITY => "QC质检",
            self::SET_PEILIAO => "设置配料信息",

        ];
    }
    
}