<?php

namespace common\components;

use Yii;
use yii\base\Component;
use common\components\goldtool\HuiTongApi;
use common\helpers\AmountHelper;
use common\models\common\GoldPrice;
use common\enums\CacheEnum;



/**
 * 金价/汇率 工具助手
 * Class Gold
 * @package common\components
 * @author gaopeng
 */
class GoldTool extends Component
{    
     //api对象
     private $api;
     
     private $exchangeRates;
     private $goldPrices;
     private $ounce = 31.1034768;
     public $dbCache  = false;
     
     public function init()
     {
         if(!$this->api) {
             $this->api = new HuiTongApi();
         }
         parent::init();
     }
     /**
      * 美元金价
      * @return string[]|number[]|\Omnipay\Alipay\AopPageGateway[]
      */
     public function getGoldUsdPrice($code = 'XAU')
     {
         if(!$this->goldPrices) {
             $this->goldPrices = $this->api->fetchGoldPriceData();
         }
         return $this->goldPrices[$code] ?? 0;
     }     
     /**
      * 汇率
      * @param string $code
      * @return number
      */
     public function getExchangeRate($code = 'USDCNY')
     {
         if(!$this->exchangeRates) {
             $this->exchangeRates = $this->api->fetchExchaneRateData();
         }
         return $this->exchangeRates[$code] ?? 0;
     }    
     /**
      * RMB金价
      * @param string $code
      * @return string
      */
     public function getGoldRmbPrice($code = 'XAU',$cha = 0)
     {
         $rmbRate = $this->getExchangeRate();
         $usdPrice = $this->getGoldUsdPrice($code);
         $rmbPrice = AmountHelper::formatAmount(($usdPrice/$this->ounce)*$rmbRate+$cha, 2);
         return $rmbPrice;
     }
     /**
      * 获取金价
      * @param string $code
      * @return number
      */
     public function getGoldPrice($code = 'XAU')
     {
         $model = $this->getDbRow($code);
         return $model['price'] ?? 0;
     }
     /**
      * 获取金价信息
      * @param string $code
      * @return \yii\db\ActiveRecord|array|NULL
      */
     private function getDbRow($code = 'XAU')
     {
         $cacheKey = CacheEnum::getPrefix('goldPrice').':'.$code;
         if (!($model = Yii::$app->cache->get($cacheKey)) || $this->dbCache == false) {
             $model = GoldPrice::find()->select(['code','price'])->where(['code'=>$code])->asArray()->one();
             Yii::$app->cache->set($cacheKey, $model,3600);
         }
         return $model;
     }
     
}