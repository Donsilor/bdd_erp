<?php

namespace addons\Sales\common\forms;

use Yii;
use common\helpers\ArrayHelper;
use addons\Sales\common\models\Order;
use common\enums\LanguageEnum;
use common\models\common\Currency;
use common\enums\CurrencyEnum;

/**
 * 订单 Form
 */
class ExternalOrderForm extends Order
{
    public $consignee_id;
    public $order_goods;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
                [['out_trade_no','consignee_id'],'required'], 
        ];
        $rules = ArrayHelper::merge(parent::rules(), $rules);
        return $rules;
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        //合并
        return ArrayHelper::merge(parent::attributeLabels() , [
             'consignee_id'=>'收货人信息'
        ]);
    } 
    
    public function getConsigneeMap()
    {   
        $map = self::getConsigneeList();
        return array_column(self::getConsigneeList(), 'title','id');
    }
    
    public static function getConsigneeList()
    {
        return [
                1 => [
                     'id'=>1,   
                     'title' =>'Unit04.23/F Universal Trade Centre 3 Arbuthrot RD Central/Mobile:+852-21653908',
                     'mobile' =>'21653908',
                     'realname' =>'香港代收点',
                     'country_id' =>279,
                     'province_id'=>0, 
                     'city_id'=>0,
                     'mobile_code'=>'+852',
                     'zip_code'=>'999077',
                     'address_details' => 'Unit04.23/F Universal Trade Centre 3 Arbuthrot RD Central',   
                ]
        ];
    }
    /**
     * 初始化FORM默认值
     */
    public function initForm() 
    {    
        //13台湾momo 7东森
         if($this->sale_channel_id == 7 || $this->sale_channel_id == 13) {
             $this->language = LanguageEnum::ZH_HK;
             $this->currency = CurrencyEnum::TWD;
         }else if($this->sale_channel_id == 8) {
             //8 HKTvMall 
             $this->language = LanguageEnum::ZH_HK;
             $this->currency = CurrencyEnum::HKD;
         }else {
             $this->language = LanguageEnum::ZH_HK;
             $this->currency = CurrencyEnum::HKD;
         }         
        
    }
    
}
