<?php

namespace addons\Sales\common\enums;

/**
 * 问题类型
 * @package common\enums
 */
class ProblemTypeEnum extends \common\enums\BaseEnum
{
    const FACTORY = 1;
    const ORDER = 2;
    const WAREHOUSE = 3;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::FACTORY => '工厂',
            self::ORDER => '订单',
            self::WAREHOUSE => '仓库',
        ];
    }

}