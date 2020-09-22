<?php

namespace addons\Gdzb\common\forms;

use addons\Gdzb\common\enums\InvoiceStatusEnum;
use addons\Sales\common\enums\InvoiceTitleTypeEnum;
use addons\Sales\common\enums\InvoiceTypeEnum;
use common\models\common\Country;
use Yii;
use common\helpers\ArrayHelper;
use addons\Gdzb\common\models\Order;


/**
 * 订单 Form
 */
class OrderConsigneeForm extends Order
{

    public $country_id;
    public $province_id;
    public $city_id;
    public $address;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['customer_name','customer_mobile','customer_weixin'], 'required'],
            [['country_id','province_id','city_id'],'integer'],
            [['address'], 'string', 'max' => 100],
        ];
        return ArrayHelper::merge(parent::rules(),$rules);
    }    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        //合并
        return ArrayHelper::merge(parent::attributeLabels() , [
            'country_id' => '国家',
            'province_id' => '省',
            'city_id' => '市',
            'address' => '地址',
        ]);
    }


    /**
     * @param $post
     * @return string
     * 设置地址
     */
    public function setConsigneeInfo($post){
        return json_encode([
            'country_id' => $post['country_id'],
            'province_id' => $post['province_id'],
            'city_id' => $post['city_id'],
            'address' => $post['address']
        ]);
    }

    /****
     * @param $model
     * 获取地址
     */
    public function getConsigneeInfo(&$model){
        $consignee_info = json_decode($model->consignee_info,true);
        $model->country_id = $consignee_info['country_id'];
        $model->province_id = $consignee_info['province_id'];
        $model->city_id = $consignee_info['city_id'];
        $model->address = $consignee_info['address'];
    }


    /**
     * 关联国家一对一
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::class, ['id'=>'country_id'])->alias("country");
    }

    /**
     * 关联省份一对一
     * @return \yii\db\ActiveQuery
     */
    public function getProvince()
    {
        return $this->hasOne(Country::class, ['id'=>'province_id'])->alias("province");
    }
    /**
     * 关联城市一对一
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(Country::class, ['id'=>'city_id'])->alias("city");
    }




}
