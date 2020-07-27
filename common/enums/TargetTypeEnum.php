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
    const SUPPLIER_GOODS_MENT = 11;//供应商审批（货品）流程
    const SUPPLIER_MATERIAL_MENT = 12;//供应商审批（原料）流程
    const BANK_PAY_F_MENT = 13;//财务-银行支付单（国际批发）审批流程
    const BANK_PAY_T_MENT = 14;//财务-银行支付单（国内电商/跨境电商）审批流程
    const BORROW_PAY_T_MENT = 15;//财务-个人因公借款单（国内电商/跨境电商）审批流程
    const BORROW_PAY_F_MENT = 16;//财务-个人因公借款单（国际批发）审批流程
    const CONTRACT_PAY_T_MENT = 17;//财务-合同款支付单（国内电商/跨境电商）审批流程
    const CONTRACT_PAY_F_MENT = 18;//财务-合同款支付单（国际批发）审批流程

    
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
                self::STYLE_STYLE => "款式审批流程", //款式
                self::STYLE_QIBAN => "起版审批流程", //起版
                self::ORDER_F_MENT => '客户订单',//客户订单（国际批发）审批流程
                self::ORDER_T_MENT => '客户订单',//客户订单（跨境电商）审批流程
                self::ORDER_Z_MENT => '客户订单',//客户订单（高端珠宝）审批流程
                self::SUPPLIER_GOODS_MENT => '供应商',//供应商审批（货品）流程
                self::SUPPLIER_MATERIAL_MENT => '供应商',//供应商审批（原料）流程
                self::BANK_PAY_F_MENT => '银行支付单',//财务-银行支付单（国际批发）审批流程
                self::BANK_PAY_T_MENT => '银行支付单',//财务-银行支付单（国内电商/跨境电商）审批流程
                self::BORROW_PAY_T_MENT => '个人因公借款单',//财务-个人因公借款单（国内电商/跨境电商）审批流程
                self::BORROW_PAY_F_MENT => '个人因公借款单',//财务-个人因公借款单（国际批发）审批流程
                self::CONTRACT_PAY_T_MENT => '合同款支付单',//财务-合同款支付单（国内电商/跨境电商）审批流程
                self::CONTRACT_PAY_F_MENT => '合同款支付单',//财务-合同款支付单（国际批发）审批流程
        ];
    }



    
}