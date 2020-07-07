<?php

namespace addons\Sales\common\enums;

use common\enums\BaseEnum;

/**
 * Class InvoiceElectronicEnum
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class InvoiceElectronicEnum extends BaseEnum
{
    const NO = 0;
    const YES = 1;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::NO => '纸质发票',
            self::YES => '电子发票',
        ];
    }
}