<?php

namespace addons\Sales\common\enums;

use common\enums\BaseEnum;

/**
 * Class InvoiceTitleTypeEnum
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class InvoiceTitleTypeEnum extends BaseEnum
{
    const NONE = 0;
    const ENTERPRISE = 1;
    const PERSONAL = 2;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::ENTERPRISE => '企业',
            self::PERSONAL => '个人',
        ];
    }
}