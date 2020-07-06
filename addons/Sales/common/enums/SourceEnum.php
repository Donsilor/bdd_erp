<?php

namespace addons\Sales\common\enums;

/**
 * 客户来源
 * @package common\enums
 */
class SourceEnum extends \common\enums\BaseEnum
{

    const BBD = 1;
    const KAD = 2;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::BBD => 'BDD官网',
            self::KAD => 'KAD官网',
        ];
    }

}