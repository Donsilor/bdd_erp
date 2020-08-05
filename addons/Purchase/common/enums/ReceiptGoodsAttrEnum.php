<?php

namespace addons\Purchase\common\enums;

/**
 * 采购单货品属性  枚举
 * @package common\enums
 */
class ReceiptGoodsAttrEnum extends BaseEnum
{
    const FINGER = 38;
    const XIANGKOU = 49;
    const MATERIAL = 10;
    const MAIN_STONE_COLOR = 7;
    const MAIN_STONE_CLARITY = 2;
    const MAIN_STONE = 56;
    const SECOND_STONE = 60;
    const BIAOMIANGONGYI = 57;

    /**
     *
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::FINGER => "指圈",
                self::XIANGKOU => "镶口",
                self::MATERIAL => "主成色",
                self::MAIN_STONE_COLOR =>"主石颜色",
                self::MAIN_STONE_CLARITY =>"主石净度",
                self::MAIN_STONE =>"主石类型",
                self::SECOND_STONE =>"副石类型",
                self::BIAOMIANGONGYI =>"表面工艺",
        ];
    }
    
}