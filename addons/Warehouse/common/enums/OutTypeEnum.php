<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 出库类型
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class OutTypeEnum extends \common\enums\BaseEnum
{
    const PEILIAO_OUT = 1;
    const OTHER_OUT = 2;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::PEILIAO_OUT => '配料出库',
            self::OTHER_OUT => '其它出库',
        ];
    }

}