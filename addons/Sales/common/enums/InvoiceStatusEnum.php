<?php

namespace addons\Sales\common\enums;

use common\enums\BaseEnum;

/**
 * Class InvoiceTitleTypeEnum
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class InvoiceStatusEnum extends BaseEnum
{
    const NO = 0;
    const YES = 1;
    
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::ENTERPRISE => '企业',
                self::PERSONAL => '个人',
                self::NONE => '不开发票',
        ];
    }
}