<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 仓库类型
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class GoodsStatusEnum extends \common\enums\BaseEnum
{
    const SHOU_HUO_ZHONG = 1;
    const KU_CUN = 2;
    const YI_XIAO_SHOU = 3;
    const PIAN_DIAN_ZHONG = 4;
    const DIAO_BO_ZHONG = 5;
    const SUN_YI_ZHONG = 6;
    const YI_BAO_SUN = 7;
    const FAN_CHANG_ZONG = 8;
    const YI_FAN_CHANG = 9;
    const XIAO_SHOU_ZHONG = 10;
    const TUI_HUO_ZHONG = 11;
    const ZUO_FEI = 12;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::SHOU_HUO_ZHONG => '收货中',
            self::KU_CUN => '库存',
            self::YI_XIAO_SHOU => '已销售',
            self::PIAN_DIAN_ZHONG => '盘点中',
            self::DIAO_BO_ZHONG => '调拨中',
            self::SUN_YI_ZHONG => '损益中',
            self::YI_BAO_SUN => '已报损',
            self::FAN_CHANG_ZONG => '返厂中',
            self::YI_FAN_CHANG => '已返厂',
            self::XIAO_SHOU_ZHONG => '销售中',
            self::TUI_HUO_ZHONG => '退货中',
            self::ZUO_FEI => '作废',

        ];
    }


}