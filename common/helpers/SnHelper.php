<?php

namespace common\helpers;

use Yii;
/**
 * Class StringHelper
 * @package common\helpers
 * @author jianyan74 <751393839@qq.com>
 */
class SnHelper 
{  
    /**
     * 订单号
     * @param unknown $order_id
     * @param string $prefix
     */
    public static function createOrderSn($prefix = 'BDD')
    {
        return $prefix.date('ymd').mt_rand(3,9).str_pad(mt_rand(1, 9999999),7,'0',STR_PAD_LEFT);
    }
    /**
     * 采购单号
     * @param string $prefix
     * @return string
     */
    public static function createPurchaseSn($prefix = 'CG')
    {
        return $prefix.date('ymd').mt_rand(3,9).str_pad(mt_rand(1, 999999),6,'0',STR_PAD_LEFT);
    }
    /**
     * 采购申请单号
     * @param string $prefix
     * @return string
     */
    public static function createPurchaseApplySn($prefix = 'CGA')
    {
        return $prefix.date('ymd').mt_rand(3,9).str_pad(mt_rand(1, 99999),5,'0',STR_PAD_LEFT);
    }
    /**
     * 布产单号
     * @param string $prefix
     * @return string
     */
    public static function createProduceSn($prefix = 'BC')
    {
        return $prefix.date('ymd').mt_rand(3,9).str_pad(mt_rand(1, 999999),6,'0',STR_PAD_LEFT);
    }
    /**
     * 单据编号
     * @param string $prefix
     * @return string
     */
    public static function createBillSn($prefix = 'B')
    {        
        $number_len = 10 - strlen($prefix);        
        $number_max = substr('9999999999',0, $number_len);
        return $prefix.date('ymd').mt_rand(3,9).str_pad(mt_rand(1, $number_max),$number_len,'0',STR_PAD_LEFT);
    }
    /**
     * 起版编号
     * @param string $prefix
     * @return string
     */
    public static function createQibanSn($prefix = 'QB')
    {
        $number_len = 6 - strlen($prefix);
        $number_max = substr('999999',0, $number_len);
        return $prefix.date('md').mt_rand(3,9).str_pad(mt_rand(1, $number_max),$number_len,'0',STR_PAD_LEFT);
    }
    /**
     * 出货单编号
     * @param string $prefix
     * @return string
     */
    public static function createShipmentSn($prefix = 'CH')
    {
        return $prefix.date('ymd').mt_rand(3,9).str_pad(mt_rand(1, 99999),5,'0',STR_PAD_LEFT);
    }
    /**
     * 采购收货单号
     * @param string $prefix
     * @return string
     */
    public static function createReceiptSn($prefix = 'SH')
    {
        return $prefix.date('ymd').mt_rand(3,9).str_pad(mt_rand(1, 999999),5,'0',STR_PAD_LEFT);
    }
    /**
     * 不良返厂单号
     * @param string $prefix
     * @return string
     */
    public static function createDefectiveSn($prefix = 'FC')
    {
        return $prefix.date('ymd').mt_rand(3,9).str_pad(mt_rand(1, 999999),5,'0',STR_PAD_LEFT);
    }
    /**
     * 库存货号生成
     * @param string $prefix
     * @return string
     */
    public static function createGoodsId($prefix = '9')
    {
        return $prefix.date('ymd').mt_rand(3,9).str_pad(mt_rand(1, 9999999),7,'0',STR_PAD_LEFT);
    }

    
}