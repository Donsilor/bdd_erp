<?php


namespace common\helpers;


class AmountHelper
{
	/**
     * 输出金额
     * @param unknown $amount
     * @param number $scale
     * @param string $sep
     * @return string
     */
    public static function formatAmount($amount, $scale = 2, $sep = null)
    {
        if(! $sep ){
            return round($amount,$scale);
        } else {
            return number_format($amount, $scale, '.', $sep);
        }
    }
    /**
     * 汇率计算
     * @param float $amount 需格式化的金额
     * @param int $rate 利率
     * @param int $scale 保留小数位数
     * @param string $sep sep
     * @return string
     */
    public static function rateAmount($amount, $rate = 1, $scale = 2, $sep = null)
    {
        $amount = bcmul($amount, $rate, ($scale+1));
        return self::formatAmount($amount, $scale, $sep);
    }    
    /**
     * 加价率计算
     * @param unknown $amount
     * @param unknown $markup_rate
     * @param unknown $markup_value
     * @param unknown $scale
     * @param string $sep
     */
    public static function calcMarkupPrice($amount, $markup_rate, $markup_value, $scale = 2,$sep = null)
    {
        $amount = bcadd(bcmul($amount, $markup_rate, $scale+2),$markup_value,$scale+2);
        return self::formatAmount($amount, $scale, $sep);
    }
    /**
     * 金额带单位输出
     * @param unknown $amount
     * @param number $scale
     * @param unknown $currency
     * @param string $sep
     * @return number|unknown
     */
    public static function outputAmount($amount,$scale = 2,$currency = 'CNY' ,$sep = ',') 
    {
        $amount = self::formatAmount($amount, $scale, $sep);
        return $currency.' '.$amount;
    }
}