<?php

namespace addons\Warehouse\common\enums;


/**
 *
 * 其它退货单-退货类型(退货原因) 对应 bill->item_type 字段
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class ReturnTypeEnum extends \common\enums\BaseEnum
{
    const TYPE_1   = 1;
    const TYPE_2   = 2;
    const OTHER    = 3;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::TYPE_1   => '客户退货',
            self::TYPE_2   => '货品问题',
            self::OTHER    => '其它原因',
        ];
    }
    
}