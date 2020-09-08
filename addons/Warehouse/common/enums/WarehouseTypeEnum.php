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
    const GuiMian = 1;
    const Houku = 2;
    const Daiqu = 3;
    const DongJie = 4;
    const ZengPin = 5;
    const LuoZuan = 6;
    const CaiHuo = 7;
    const TuiHuo = 8;
    const JieHuo = 9;
    const QiTa = 10;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::GuiMian => '柜面',
            self::Houku => '后库',
            self::Daiqu => '待取',
            self::DongJie => '冻结',
            self::ZengPin => '赠品',
            self::LuoZuan => '祼钻',
            self::CaiHuo => '拆货',
            self::TuiHuo => '退货',
            self::JieHuo => '借货',
            self::QiTa => '其它',
        ];
    }

}