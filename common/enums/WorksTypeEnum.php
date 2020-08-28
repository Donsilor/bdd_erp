<?php

namespace common\enums;

/**
 * Class SortEnum
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class WorksTypeEnum extends BaseEnum
{
    const DAY_SUMMARY = 1;
//    const DAY_WEEK = 2;
//    const DAY_MONTH = 3;


    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::DAY_SUMMARY => '日报',
//            self::DAY_WEEK => '周报',
//            self::DAY_MONTH => '月报',
        ];
    }
}