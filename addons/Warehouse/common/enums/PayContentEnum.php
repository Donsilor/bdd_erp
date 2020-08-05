<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 收货单支付内容
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class PayContentEnum extends \common\enums\BaseEnum
{
    const GOLD_FEE      = 1;
    const STONE_FEE     = 2;
    const CHENG_PIN_FEE = 3;
    const CERT_FEE      = 4;
    const JIA_GONG_FEE  = 5;
    const PARTS_FEE     = 6;
    const SERVE_FEE     = 7;
    const DIFFER        = 8;


    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::GOLD_FEE        => '金料',
            self::STONE_FEE       => '石料',
            self::CHENG_PIN_FEE   => '成品',
            self::CERT_FEE        => '证书费',
            self::JIA_GONG_FEE    => '加工费',
            self::PARTS_FEE       => '配件费',
            self::SERVE_FEE       => '服务费',
            self::DIFFER          => '差',
        ];
    }

}