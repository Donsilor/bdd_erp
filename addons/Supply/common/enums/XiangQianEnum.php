<?php

namespace addons\Supply\common\enums;

use common\enums\BaseEnum;

/**
 * 镶嵌类型  枚举
 * @package common\enums
 */
class XiangQianEnum extends BaseEnum
{
    const DIAMOND_MOSAIC = 1;
    const NO_MOSAIC  = 2;
    const MOSAIC  = 3;
    const WATCH_MOSAIC  = 4;
    const FINISHED_PRODUCT  = 5;
    const SEMI_FINISHED  = 6;

    /**
     * 
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::DIAMOND_MOSAIC => "工厂配钻，工厂镶嵌",
            self::NO_MOSAIC => "不需工厂镶嵌",
            self::MOSAIC => "需工厂镶嵌",
            self::WATCH_MOSAIC => "客户先看钻再返厂镶嵌",
            self::FINISHED_PRODUCT => "成品",
            self::SEMI_FINISHED => "半成品",

        ];
    }
    
}