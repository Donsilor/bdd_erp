<?php

namespace common\components\goldtool;

use Yii;

/**
 * Class HuiTongApi
 * @package common\components\payment
 */
class RongTongApi
{
    //金价API
    const GOLD_PRICE_API = 'http://beijingrtj.com/admin/get_price5.php?&t=1597047471052';   
    /**
     * 获取 金价
     * @return \Omnipay\Alipay\AopPageGateway
     */
    public function fetchGoldPriceData()
    {
        $txtData  = self::curlHttpd(self::GOLD_PRICE_API);
        $array = explode(',',$txtData);
        $data = [
            'XAU' =>$array[2] ?? 0, 
            'XAG' =>$array[4] ?? 0, 
            'XPT' =>$array[6] ?? 0, 
        ];
        return $data;
    }
    /**
     * 获取实时汇率
     * @return \Omnipay\Alipay\AopPageGateway
     */
    public function fetchExchaneRateData()
    {
        $jsonData  = self::curlHttpd(self::EXCHANGE_RATE_API);
        $data = json_decode($jsonData,true) ?? [];
        return array_column($data, 'c','i');
    }    
    /**
     * 请求API
     * @param unknown $url
     * @param string $user_agent
     * @param string $refer
     * @return mixed
     */
    function curlHttpd($url, $user_agent='', $refer=''){
        $ci = curl_init();
        $user_agent = ($user_agent==''||$user_agent=='baidu')?"Baiduspider+(+http://www.baidu.com/search/spider.htm)":$user_agent;
        $refer= $refer==''?"http://www.baidu.com":$refer;
        $ip ='113.128.18.'.rand(1,255);
        $ips = array('X-FORWARDED-FOR:'.$ip, 'CLIENT-IP:'.$ip);
        curl_setopt($ci, CURLOPT_HTTPHEADER,$ips);
        curl_setopt($ci, CURLOPT_URL, $url);
        curl_setopt($ci, CURLOPT_HEADER, false);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ci, CURLOPT_REFERER,$refer);
        curl_setopt($ci, CURLOPT_USERAGENT, $user_agent);
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($ci);
        curl_close($ci);
        return $data;
    }
}
