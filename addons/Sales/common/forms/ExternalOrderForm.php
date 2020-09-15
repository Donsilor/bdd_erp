<?php

namespace addons\Sales\common\forms;

use Yii;
use common\helpers\ArrayHelper;
use addons\Sales\common\models\Order;

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
        $rules = [];
        return ArrayHelper::merge(parent::rules(),$rules);
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
        return [
              1=>'亚马逊代收:广东省深圳市XXX区2004栋003号/高朋/15989407534'  
        ];
    }
    
    public static function getConsigneeList()
    {
        $data = [
                1 => [
                     'title' =>'亚马逊代收:广东省深圳市XXX区2004栋003号/高朋/15989407534',
                     'customer_mobile' =>'15989407534',
                     'customer_name' =>'高朋',
                     'country_id' =>0,
                     'province_id'=>0, 
                     'city_id'=>0,
                     'address_details' => '广东省深圳市XXX区2004栋003号',   
                ]
        ];
    }
    
}
