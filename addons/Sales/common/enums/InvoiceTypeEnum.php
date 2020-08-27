<?php

namespace addons\Sales\common\enums;

use common\enums\BaseEnum;

/**
 * Class InvoiceTypeEnum
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class InvoiceTypeEnum extends BaseEnum
{
    const PAPER = 1;
    const ELECTRONIC = 3;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [           
            self::ELECTRONIC => '电子发票',
            self::PAPER => '纸质发票',          
        ];
    }
}