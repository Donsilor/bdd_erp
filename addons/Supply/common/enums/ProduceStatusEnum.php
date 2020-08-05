<?php

namespace addons\Supply\common\enums;

use common\enums\BaseEnum;
/**
 * 生成状态  枚举
 * @package common\enums
 */
class ProduceStatusEnum extends BaseEnum
{
    const TO_PRODUCED = 1;
    const START_PRODUCED = 2;
    const SEND_DRILL = 3;
    const QC_PASSED = 4;
    const QC_NOT_PASSED = 5;
    const LEAVE_FACTORY = 6;
    const STARTING_VERSION = 7;
    const REVISION = 8;
    const INVERSION = 9;
    const MODEL = 10;
    const WAIT_DRILL = 11;
    const INLAY = 12;
    const POLISHING = 13;
    const ELECTRIC_GOLD = 14;
    /**
     * 
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::TO_PRODUCED => "待生产",
            self::START_PRODUCED => "开始生产",
            self::SEND_DRILL => "送钻",
            self::QC_PASSED => "QC质检通过",
            self::QC_NOT_PASSED => "QC质检未过",
            self::LEAVE_FACTORY => "出厂",
            self::STARTING_VERSION => "起版",
            self::REVISION => "修版",
            self::INVERSION => "倒模",
            self::MODEL => "执模",
            self::WAIT_DRILL => "等钻",
            self::INLAY => "镶石",
            self::POLISHING => "抛光",
            self::ELECTRIC_GOLD => "电金",

        ];
    }
    
}