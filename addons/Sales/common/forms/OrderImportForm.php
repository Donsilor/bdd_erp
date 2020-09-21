<?php

namespace addons\Sales\common\forms;

use Yii;
use common\models\forms\ImportForm;
use addons\Sales\common\models\Order;
use addons\Sales\common\models\Platform;
use common\enums\LanguageEnum;
use addons\Sales\common\models\Currency;
use common\enums\CurrencyEnum;
use addons\Style\common\models\Style;

/**
 * 订单 Form
 */
class OrderImportForm extends ImportForm
{
    public $file;
    
    //表格数据
    public $platform;
    public $out_trade_no;
    public $style_sn_1;
    public $style_1;//
    public $goods_name_1;
    public $size_1;
    public $finger_type_1;
    public $finger_1;
    public $goods_spec_1;//
    public $goods_price_1;
    public $style_sn_2;
    public $style_2;//
    public $goods_name_2;
    public $size_2;
    public $finger_type_2;
    public $finger_2;
    public $goods_spec_2;//
    public $goods_price_2;
    public $language;
    public $currency;
    public $other_fee;
    public $order_amount;
    public $arrive_amount;
    public $order_time;
    public $customer_mobile;
    public $pay_remark;
    public $remark;
  
    public $columns = [
            1=>'platform',
            2=>'out_trade_no',
            3=>'style_sn_1',
            4=>'goods_name_1',
            5=>'size_1',
            6=>'finger_type_1',
            7=>'finger_1',
            8=>'goods_price_1',
            9=>'style_sn_2',
            10=>'goods_name_2',
            11=>'size_2',
            12=>'finger_type_2',
            13=>'finger_2',
            14=>'goods_price_2',
            15=>'language',
            16=>'currency',
            17=>'other_fee',
            18=>'order_amount',
            19=>'arrive_amount',
            20=>'order_time',
            21=>'customer_mobile',
            22=>'pay_remark',
            23=>'remark'
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
                'platform',
                'out_trade_no',
                'style_sn_1',
                'goods_name_1',                
                'goods_price_1',
                'currency',
                'arrive_amount',
                'order_time',
        ];
        $numberColumns = [
                'goods_price_1',
                'goods_price_2',
                'other_fee',
                'order_amount',
                'arrive_amount'
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
                $this->addRowError($rowIndex, 'order_time', "[{$this->order_time}]填写错误");
            }else{
                $this->order_time = $order_time;
            }
        }
        //订单编号校验
        if($this->out_trade_no) {
            if($count = Order::find()->where(['out_trade_no'=>$this->out_trade_no])->count()) {
                $this->addRowError($rowIndex, 'out_trade_no', "[{$this->out_trade_no}]重复导入");
            }
        }
        //销售平台校验
        if($this->platform) {
            $platform = Platform::find()->where(['name'=>$this->platform])->one();
            if(!$platform) {
                $this->addRowError($rowIndex, 'platform', "[{$this->platform}]不存在");
            }else{
                $this->platform = $platform;
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
        if($this->style_sn_1) {
            $style_1 = Style::find()->where(['style_sn'=>$this->style_sn_1])->one();
            if(!$style_1) {
                $this->addRowError($rowIndex, 'style_sn_1', "[{$this->style_sn_1}]不存在");
            }else if($style_1->status != 1) {
                $this->addRowError($rowIndex, 'style_sn_1', "[{$this->style_sn_1}]不是启用状态");
            }else{
                $this->style_1 = $style_1;
            }
            $goods_spec = [];
            if($this->size_1) {
                $goods_spec['尺寸(cm)'] = $this->size_1;
            }
            if($this->finger_1) {
                $goods_spec['手寸'] = $this->finger_type_1."#".trim($this->finger_1,'#');
            }
            $this->goods_spec_1 = $goods_spec ? json_encode($goods_spec) : null;
        }
        //产品2信息
        if($this->style_sn_2) {
            $style_2 = Style::find()->where(['style_sn'=>$this->style_sn_2])->one();
            if(!$style_2) {
                $this->addRowError($rowIndex, 'style_sn_2', "[{$this->style_sn_2}]不存在");
            }else if($style_2->status != 1) {
                $this->addRowError($rowIndex, 'style_sn_2', "[{$this->style_sn_2}]不是启用状态");
            }else{
                $this->style_2 = $style_2;
            }
            
            $goods_spec = [];
            if($this->size_2) {
                $goods_spec['尺寸(cm)'] = $this->size_2;
            }
            if($this->finger_2) {
                $goods_spec['手寸'] = $this->finger_type_2."#".trim($this->finger_2,'#');
            }
            $this->goods_spec_2 = $goods_spec ? json_encode($goods_spec) : null;
        }
        
        return empty($this->getRowErrors()) ? true: false;
    }    
}
