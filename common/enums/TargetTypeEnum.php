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
    const PURCHASE_APPLY_T_MENT = 2;//采购申请单（电商）审批流程
    const PURCHASE_APPLY_F_MENT = 3;//采购申请单（国际批发）审批流程
    const PURCHASE_APPLY_Z_MENT = 4;//采购申请单（高端珠宝）审批流程
    const PURCHASE_APPLY_S_MENT = 5;//采购申请单（商品部）审批流程

    
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::PURCHASE_MENT => "采购单据",
                self::PURCHASE_APPLY_T_MENT => "采购申请单（电商）审批流程",
                self::PURCHASE_APPLY_F_MENT => "采购申请单（国际批发）审批流程",
                self::PURCHASE_APPLY_Z_MENT => "采购申请单（高端珠宝）审批流程",
                self::PURCHASE_APPLY_S_MENT => "采购申请单（商品部）审批流程",
        ];
    }



    
}