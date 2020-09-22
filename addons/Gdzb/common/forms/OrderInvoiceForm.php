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
class OrderInvoiceForm extends Order
{

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
            [[], 'required'],
            [['invoice_type','title_type','invoice_status'],'integer'],
            [['invoice_title','tax_number','email'], 'string', 'max' => 100],
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



}
