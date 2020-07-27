<?php

namespace common\enums;

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
    const STYLE_STYLE = 6;//款式
    const STYLE_QIBAN = 7;//起版
    const ORDER_F_MENT = 8;//客户订单（国际批发）审批流程
    const ORDER_T_MENT = 9;//客户订单（跨境电商）审批流程
    const ORDER_Z_MENT = 10;//客户订单（高端珠宝）审批流程

    
    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
                self::PURCHASE_MENT => "采购单据",
                self::PURCHASE_APPLY_T_MENT => "采购申请单",//采购申请单（电商）审批流程
                self::PURCHASE_APPLY_F_MENT => "采购申请单",//采购申请单（国际批发）审批流程
                self::PURCHASE_APPLY_Z_MENT => "采购申请单",//采购申请单（高端珠宝）审批流程
                self::PURCHASE_APPLY_S_MENT => "采购申请单",//采购申请单（商品部）审批流程
                self::STYLE_STYLE => "款式审批流程",
                self::STYLE_QIBAN => "起版审批流程",
                self::ORDER_F_MENT => '客户订单',
                self::ORDER_T_MENT => '客户订单',
                self::ORDER_Z_MENT => '客户订单',
        ];
    }



    
}