<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 维修动作
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class RepairActEnum extends \common\enums\BaseEnum
{
    const HUANSHI       = 1;
    const XIANGSHI      = 2;
    const FANXIN        = 3;
    const LASHA         = 4;
    const PENSHA        = 5;
    const KEZI          = 6;
    const MOZI          = 7;
    const MOZICHONGKE   = 8;
    const GAIQUAN       = 9;
    const PAOGUANG      = 10;
    const DIANJIN       = 11;
    const JIAFUSHI      = 12;
    const JIAGUZUANSHI  = 13;
    const HESHI         = 14;
    const ZUOZHENGSHU   = 15;
    const XIUZHUA       = 16;
    const BIANSHENG     = 17;
    const HANJIE        = 18;
    const QITA          = 100;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::HUANSHI       => '换石',
            self::XIANGSHI      => '镶石',
            self::FANXIN        => '翻新',
            self::LASHA         => '拉沙',
            self::PENSHA        => '喷砂',
            self::KEZI          => '刻字',
            self::MOZI          => '抹字',
            self::MOZICHONGKE   => '抹字重刻',
            self::GAIQUAN       => '改圈',
            self::PAOGUANG      => '抛光',
            self::DIANJIN       => '电金',
            self::JIAFUSHI      => '加副石',
            self::JIAGUZUANSHI  => '加固钻石',
            self::HESHI         => '核实',
            self::ZUOZHENGSHU   => '做证书',
            self::XIUZHUA       => '修爪',
            self::BIANSHENG     => '编绳',
            self::HANJIE        => '焊接',
            self::QITA          => '其它',
        ];
    }

}