<?php

namespace addons\Sales\common\forms;

use Yii;
use common\models\forms\ImportForm;
use common\enums\LanguageEnum;
use addons\Sales\common\models\Currency;
use common\enums\CurrencyEnum;
use addons\Style\common\models\Style;

/**
 * 国际批发订单导入  Form
 */
class OrderImportKForm extends ImportForm
{
    public $file;
    
    //表格数据
    public $order_sn;
    public $channel_id;
    public $follower_id;
    public $order_time;
    public $pay_time;
    public $language;
    public $currency;
    public $pay_amount;
    public $paid_amount;
    public $customer_no;
    public $customer_mobile;
    public $customer_email;
    public $style_sn;
    public $goods_name;
    public $material_type;
    public $material_color;
    public $goods_num;
    public $goods_price;
    public $goods_weight;
    public $finger;
    public $finger_hk;
    public $size;
    public $stone_carat;
    public $stone_num;
    public $stone_color;
    public $stone_clarity;
    public $stone_price;
    public $second_stone_weight1;
    public $second_stone_num1;
    public $second_stone_price1;
    public $stone_spec;
    public $remark;
    
    public $columns = [
            1=>'order_sn',
            2=>'channel_id',
            3=>'follower_id',
            4=>'order_time',
            5=>'language',
            6=>'currency',
            7=>'pay_amount',
            8=>'paid_amount',
            9=>'customer_no',
            10=>'customer_mobile',
            11=>'customer_email',
            12=>'style_sn',
            13=>'goods_name',
            14=>'material_type',
            15=>'material_color',
            16=>'goods_num',
            17=>'goods_price',
            18=>'goods_weight',
            19=>'finger',
            20=>'finger_hk',
            21=>'size',
            22=>'stone_carat',
            23=>'stone_num',
            24=>'stone_color',
            25=>'stone_clarity',
            26=>'stone_price',
            27=>'second_stone_weight1',
            28=>'second_stone_num1',
            29=>'second_stone_price1',
            30=>'stone_spec',
            31=>'remark',
    ];
    public $titles;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
                [['file'], 'required','isEmpty'=>function($value){
                    return !empty($this->file);
                }],
                [['file'], 'file', 'extensions' => ['xlsx']],
         ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        //合并
        return [
                'file'=>'上传文件',
        ];
    }
    /**
     * 校验 excel 行
     * @param array $row 行数据
     * @return boolean
     */
    public function loadRow($row,$rowIndex)
    {
        $requredColumns = [
                'out_trade_no',
                'channel_id',
                'order_time',
                'language',
                'currency',
                'pay_amount',
                'paid_amount',
                'customer_email',
                'style_sn',
                'goods_num',
                'goods_price',                
        ];
        $numberColumns = [
                'goods_price',
                'pay_amount',
                'paid_amount',
                'stone_price',
                'second_stone_price1'
        ];
        foreach ($row as $attribute=> $colValue) {
            //必填校验
            if(in_array($attribute,$requredColumns) && $colValue === '') {
                $this->addRowError($rowIndex, $attribute, "不能为空");
            }
            //数字校验
            if(in_array($attribute,$numberColumns) && $colValue != '') {
                if(is_numeric($colValue) && $colValue >= 0) {
                    $row[$attribute] = $colValue;
                }else {
                    $this->addRowError($rowIndex, $attribute, "[{$colValue}]必须为数字");
                }
            }
            $this->{$attribute} = trim($colValue);
            
        }
        //下单时间
        if($this->order_time) {
            $order_time = @strtotime($this->order_time);
            if($order_time <= strtotime('2000-01-01')) {
                $this->addRowError($rowIndex, 'pay_time', "[{$this->order_time}]填写错误");
            }else{
                $this->order_time = $order_time;
            }
            if($this->paid_amount > 0) {
                $this->pay_time = $order_time;
            }
        }
        //订单语言
        if($this->language) {
            $language = LanguageEnum::getValue($this->language,'getFlipMap');
            if(!$language) {
                $this->addRowError($rowIndex, 'language', "[{$this->language}]填写错误");
            }else{
                $this->language = $language;
            }
        }else {
            $this->language = $this->platform->language ?? '';
        }
        //订单货币
        if($this->currency) {
            $currency = CurrencyEnum::getValue($this->currency);
            if(!$currency) {
                $this->addRowError($rowIndex, 'currency', "[{$this->currency}]填写错误");
            }else{
                $this->currency = $currency;
            }
        }
        //产品1信息
        if($this->style_sn) {
            $style = Style::find()->where(['style_sn'=>$this->style_sn_1])->one();
            if(!$style) {
                $this->addRowError($rowIndex, 'style_sn', "[{$this->style_sn}]不存在");
            }else if($style_1->status != 1) {
                $this->addRowError($rowIndex, 'style_sn', "[{$this->style_sn_1}]不是启用状态");
            }else{
                $this->style = $style;
            }            
        }
        
        
        return empty($this->getRowErrors()) ? true: false;
    }
}
