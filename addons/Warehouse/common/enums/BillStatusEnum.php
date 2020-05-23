<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 仓储单据状态
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class BillStatusEnum extends \common\enums\BaseEnum
{
    const SAVE     = 0;
    const AUDIT    = 1;
    const CANCEL   = 2;
    const SIGN_FOR = 3;

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::SAVE      => '已保存',
            self::AUDIT     => '已审核',
            self::CANCEL    => '已取消',
            self::SIGN_FOR  => '已签收',
        ];
    }

}