<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 仓库类型
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class PutInTypeEnum extends \common\enums\BaseEnum
{
    const GOU_MAI = 1;
    const JIA_GONG = 2;
    const DAI_XIAO = 3;
    const JIE_RU = 4;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::GOU_MAI => '购买',
            self::JIA_GONG => '委托加工',
            self::DAI_XIAO => '代销',
            self::JIE_RU => '借入',
        ];
    }

}