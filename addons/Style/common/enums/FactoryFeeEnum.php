<?php

namespace addons\Style\common\enums;

/**
 * 加工费类型 枚举
 * @package common\enums
 */
class FactoryFeeEnum extends \common\enums\BaseEnum
{
    const BASIC_GF = 1;
    const INLAID_GF = 2;
    const PARTS_GF = 3;
    const GEAM_GF = 4;
    const PEISHI_GF = 5;
    const FENSE_GF = 6;
    const PENSHA_GF = 7;
    const LASHA_GF = 8;
    const BUKOU_GF = 9;
    const TEMPLET_GF = 10;
    const CERT_GF = 11;
    const CHEHUAPIAN_GF = 12;
    const FENJIAN_GF = 13;
    const LUZHUBIAN_GF = 14;
    const TECHNOLOGY_GF = 15;

    const OTHER_GF = 99;
    /**
     * @return array
     *
     */
    public static function getMap(): array
    {
        return [
            self::BASIC_GF => "基本工费",
            self::INLAID_GF => "镶石费/颗",
            self::PARTS_GF => "配件工费",
            self::GEAM_GF => "克/工费",
            self::PEISHI_GF => "配石工费/ct",
            self::FENSE_GF => "分色费",
            self::PENSHA_GF => "喷沙费",
            self::LASHA_GF => "拉沙费",
            self::BUKOU_GF => "补口费",
            self::TEMPLET_GF => "版费",
            self::CERT_GF => "证书费",
            self::CHEHUAPIAN_GF => "车花片",
            self::FENJIAN_GF => "分件费",
            self::LUZHUBIAN_GF => "辘珠边",
            self::TECHNOLOGY_GF => "表面工艺费",

            self::OTHER_GF => "其它费用",

        ];
    }

}