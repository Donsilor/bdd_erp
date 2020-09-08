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
    const TECHNOLOGY_GF = 4;
    const GEAM_GF = 5;
    const PEISHI_GF = 6;
    const FENSE_GF = 7;
    const PENLASHA_GF = 8;
    const BUKOU_GF = 9;
    const TEMPLET_GF = 10;
    const CERT_GF = 11;

    const OTHER_GF = 99;
    /**
     * @return array
     *
     */
    public static function getMap(): array
    {
        return [
            self::BASIC_GF => "基本工费",
            self::INLAID_GF => "镶石工费",
            self::PARTS_GF => "配件工费",
            self::TECHNOLOGY_GF => "工艺工费",
            self::GEAM_GF => "克/工费",
            self::PEISHI_GF => "配石工费",
            self::FENSE_GF => "分色费",
            self::PENLASHA_GF => "喷拉沙费",
            self::BUKOU_GF => "补口费",
            self::TEMPLET_GF => "版费",
            self::CERT_GF => "证书费",

            self::OTHER_GF => "其它费用",

        ];
    }

}