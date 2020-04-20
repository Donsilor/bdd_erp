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
        return $prefix.date('ymd').mt_rand(3,9).str_pad(mt_rand(1, 99999),6,'1',STR_PAD_LEFT);
    }
    /**
     * 采购单号
     * @param string $prefix
     * @return string
     */
    public static function createPurchaseSn($prefix = 'CG')
    {
        return $prefix.date('ymd').mt_rand(3,9).str_pad(mt_rand(1, 9999999),6,'1',STR_PAD_LEFT);
    }
    /**
     * 布产单号
     * @param string $prefix
     * @return string
     */
    public static function createProduceSn($prefix = 'BC')
    {
        return $prefix.date('ymd').mt_rand(3,9).str_pad(mt_rand(1, 9999999),6,'1',STR_PAD_LEFT);
    }
    /**
     * 单据编号
     * @param string $prefix
     * @return string
     */
    public static function createBillSn($prefix = 'B')
    {
        return $prefix.date('ymd').mt_rand(3,9).str_pad(mt_rand(1, 99999),6,'1',STR_PAD_LEFT);
    }
    
}