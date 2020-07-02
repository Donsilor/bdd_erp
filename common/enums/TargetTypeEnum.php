<?php

namespace common\enums;
use addons\Purchase\common\models\Purchase;
use common\helpers\Url;

/**
 * 目标类型
 *
 * Class StatusEnum
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class TargetTypeEnum extends BaseEnum
{
    const PURCHASE_MENT = 1;//采购单据

    
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::PURCHASE_MENT => "采购单据",
        ];
    }



    
}