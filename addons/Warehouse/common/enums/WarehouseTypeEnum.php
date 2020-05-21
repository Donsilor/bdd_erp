<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 仓库类型
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseTypeEnum extends \common\enums\BaseEnum
{
    const GUI_MIAN = 1;
    const HOU_KU = 2;
    const DAI_QU = 3;
    const DONG_JIE = 4;
    const ZENG_PIN = 5;
    const LUO_ZUAN = 6;
    const CAI_HUO = 7;
    const TUI_HUO = 8;
    const JIE_HUO = 9;
    const QI_TA = 10;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::GUI_MIAN => '柜面',
            self::HOU_KU => '后库',
            self::DAI_QU => '待取',
            self::DONG_JIE => '冻结',
            self::ZENG_PIN => '赠品',
            self::LUO_ZUAN => '祼钻',
            self::CAI_HUO => '拆货',
            self::TUI_HUO => '退货',
            self::JIE_HUO => '借货',
            self::QI_TA => '其它',
        ];
    }

}