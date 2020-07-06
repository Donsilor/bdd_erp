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
    const PURCHASE = 1;
    const PROXY_PRODUCE = 2;
    const PROXY_SALE = 3;
    const BORROW = 4;
    const OTHER = 10;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::PURCHASE => '购买',
            self::PROXY_PRODUCE => '委托加工',
            self::PROXY_SALE => '代销',
            self::BORROW => '借入',
            self::OTHER => '其他',
        ];
    }

}