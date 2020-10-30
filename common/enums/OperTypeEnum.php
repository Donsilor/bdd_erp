<?php

namespace common\enums;

/**
 * 业务类型
 *
 * Class StatusEnum
 * @package common\enums
 * @author jianyan74 <751393839@qq.com>
 */
class OperTypeEnum extends BaseEnum
{

    const PURCHASE_APPLY = 1;//采购申请单
    const PURCHASE = 2;//采购订单
    const STYLE = 3;//款式列表
    const QIBAN = 4;//起版列表
    const ORDER = 5;//客户订单
    const SUPPLIER = 6;//供应商列表
    const BANK_PAY = 7;//财务-银行支付单
    const CONTRACT_PAY = 8;//财务-个人因公借款单
    const BORROW_PAY = 9;//财务-合同款支付单

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            self::PURCHASE_APPLY => "采购申请单",
            self::PURCHASE => "采购订单",
            self::STYLE => "款式列表",
            self::QIBAN => "起版列表",
            self::ORDER => "客户订单",
            self::SUPPLIER => "供应商列表",
            self::BANK_PAY => "财务-银行支付单",
            self::CONTRACT_PAY => "财务-个人因公借款单",
            self::BORROW_PAY => "财务-合同款支付单",
        ];
    }

    /**
     * @return array
     */
    public static function getUrl(): array
    {
        return [
            self::PURCHASE_APPLY => "purchase/purchase-apply/view",
            self::PURCHASE => "purchase/purchase/view",
            self::STYLE => "style/style/view",
            self::QIBAN => "style/qiban/view",
            self::ORDER => "sales/order/view",
            self::SUPPLIER => "supply/supplier/view",
            self::BANK_PAY => "finance/bank-pay/view",
            self::CONTRACT_PAY => "finance/contract-pay/view",
            self::BORROW_PAY => "finance/borrow-pay/view",
        ];
    }

    /**
     * @param $key
     * @return string
     */
    public static function getUrlValue($key)
    {
        $map = self::getUrl();
        return $map[$key] ?? null;
    }
}