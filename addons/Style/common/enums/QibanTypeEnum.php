<?php

namespace addons\Style\common\enums;

use common\enums\BaseEnum;

/**
 * 表面工艺  枚举
 * @package common\enums
 */
class QibanTypeEnum extends BaseEnum
{
    const NON_VERSION = 0;
    const HAVE_STYLE = 1;
    const NO_STYLE = 2;

    
    /**
     *
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::NON_VERSION => "非起版",
                self::HAVE_STYLE => "有款起版",
                self::NO_STYLE => "无款起版",
        ];
    }
    
}