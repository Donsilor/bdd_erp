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
class OrderForm extends Order
{

    public $country_id;
    public $province_id;
    public $city_id;
    public $address;
    public $invoice_type;
    public $title_type;
    public $invoice_title;
    public $tax_number;
    public $email;
    public $invoice_status;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['channel_id','warehouse_id','customer_name','customer_mobile','customer_weixin','collect_type',
                'collect_no'], 'required'],
            [['country_id','province_id','city_id','supplier_id','invoice_type','title_type','invoice_status'],'integer'],
            [['address','invoice_title','tax_number','email'], 'string', 'max' => 100],
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
            'invoice_type' => '发票类型',
            'title_type' => '抬头类型',
            'invoice_title' => '发票抬头',
            'tax_number' => '纳税人识别号',
            'email' => '发送邮箱',
            'invoice_status' => '发票状态',
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
     * @param $post
     * @return string
     * 设置地址
     */
    public function setInvoiceInfo($post){
        return json_encode([
            'invoice_type' => $post['invoice_type'],
            'title_type' => $post['title_type'],
            'invoice_title' => $post['invoice_title'],
            'tax_number' => $post['tax_number'],
            'invoice_status' => $post['invoice_status'],
            'email' => $post['email'],
        ]);
    }

    /****
     * @param $model
     * 获取地址
     */
    public function getInvoiceInfo(&$model){
        $invoice_info = json_decode($model->invoice_info,true);
        $model->invoice_type = $invoice_info['invoice_type'] ?? InvoiceTypeEnum::ELECTRONIC;
        $model->title_type = $invoice_info['title_type'] ?? InvoiceTitleTypeEnum::PERSONAL;
        $model->invoice_title = $invoice_info['invoice_title'];
        $model->tax_number = $invoice_info['tax_number'];
        $model->invoice_status = $invoice_info['invoice_status'] ?? InvoiceStatusEnum::TO_INVOICE;
        $model->email = $invoice_info['email'];
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
