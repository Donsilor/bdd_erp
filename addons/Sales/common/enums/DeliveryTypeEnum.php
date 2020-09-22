<?php

namespace addons\Sales\common\enums;

use common\enums\BaseEnum;

/**
 * 发货方式
 * @package common\enums
 */
class DeliveryTypeEnum extends BaseEnum
{    
    const Platform = 1;
    const HongKong = 2;
    const Customer = 3;
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::Platform =>'平台代发',
                self::HongKong =>'香港代发',
                self::Customer =>'直发客户'
        ];
    }
    
}