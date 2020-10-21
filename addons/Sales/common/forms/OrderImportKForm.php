<?php

namespace addons\Sales\common\forms;

use Yii;
use common\models\forms\ImportForm;
use common\enums\LanguageEnum;
use addons\Sales\common\models\Currency;
use common\enums\CurrencyEnum;
use addons\Style\common\models\Style;
use addons\Sales\common\models\SaleChannel;
use addons\Sales\common\models\Customer;
use common\models\member\Account;
use addons\Sales\common\models\Order;
use addons\Sales\common\models\OrderAccount;
use addons\Sales\common\models\OrderAddress;
use addons\Style\common\enums\AttrIdEnum;
use common\helpers\ArrayHelper;
use common\models\backend\Member;
use common\helpers\AmountHelper;
use addons\Sales\common\models\OrderInvoice;

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
    public $other_fee;
    public $order_amount;
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
    public $main_stone_type;
    public $main_stone_weight;
    public $main_stone_num;
    public $main_stone_secai;
    public $main_stone_color;
    public $main_stone_clarity;
    public $main_stone_price;
    public $second_stone_type1;
    public $second_stone_weight1;
    public $second_stone_num1;
    public $second_stone_price1;
    public $stone_spec;
    public $remark;
    public $goods_spec;
    public $columns = [
            1=>'order_sn',
            2=>'channel_id',
            3=>'follower_id',
            4=>'order_time',
            5=>'language',
            6=>'currency',
            7=>'other_fee',
            8=>'order_amount',
            9=>'paid_amount',
            10=>'customer_no',
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
            22=>'main_stone_type',
            23=>'main_stone_weight',
            24=>'main_stone_num',
            25=>'main_stone_secai',
            26=>'main_stone_color',
            27=>'main_stone_clarity',
            28=>'main_stone_price',
            29=>'second_stone_type1',
            30=>'second_stone_weight1',
            31=>'second_stone_num1',
            32=>'second_stone_price1',
            33=>'stone_spec',
            34=>'remark',
    ];
    //多行合并一行的依赖字段
    public $combineKey = 'order_sn';
    //只需要填写第一行的字段
    public $combineColumns = [
            'channel_id',
            'follower_id',
            'order_time',
            'language',
            'currency',
            'other_fee',
            'order_amount',
            'paid_amount',
            'customer_no',
            'customer_mobile',
            'customer_email',
    ];
    public $requredColumns = [
            'out_trade_no',
            'channel_id',
            'order_time',
            'language',
            'currency',
            'order_amount',
            'paid_amount',
            'customer_email',
            'style_sn',
            'goods_num',
            'goods_price',
    ];
    public $numberColumns = [
            'goods_price',
            'order_amount',
            'paid_amount',
            'main_stone_price',            
            'main_stone_weight',
            'main_stone_num',
            'second_stone_num1',
            'second_stone_weight1',
            'second_stone_price1',
    ];
    //文本属性
    public $attrInputColumns = [
            AttrIdEnum::CHAIN_LENGTH =>'size',
            AttrIdEnum::MAIN_STONE_WEIGHT =>'main_stone_weight',
            AttrIdEnum::MAIN_STONE_NUM =>'main_stone_num',
            AttrIdEnum::SIDE_STONE1_WEIGHT =>'second_stone_weight1',
            AttrIdEnum::SIDE_STONE1_NUM =>'second_stone_num1',
    ];
    //单选下拉属性
    public $attrSelectColumns = [
            AttrIdEnum::MATERIAL_TYPE =>'material_type',
            AttrIdEnum::MATERIAL_COLOR =>'material_color',
            AttrIdEnum::FINGER =>'finger',
            AttrIdEnum::FINGER_HK =>'finger_hk',
            AttrIdEnum::MAIN_STONE_TYPE =>'main_stone_type',
            AttrIdEnum::MAIN_STONE_SECAI =>'main_stone_secai',
            AttrIdEnum::MAIN_STONE_COLOR=>'main_stone_color',
            AttrIdEnum::MAIN_STONE_CLARITY =>'main_stone_clarity',
            AttrIdEnum::SIDE_STONE1_TYPE =>'second_stone_type1',            
    ];  
    //客户临时缓存信息
    private $_customer;
    private $_style;
    private $_order;
    private $_customerCache;
    private $_channelCache;
    
    
    public $order_list;
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
         parent::loadRow($row, $rowIndex);         
         //销售渠道
         if(!$this->_channelCache){
             $channelMap = Yii::$app->salesService->saleChannel->getDropDown();
             $this->_channelCache = array_flip($channelMap);
         }
         if($this->channel_id) {
             if(isset($this->_channelCache[$this->channel_id])) {
                 $this->channel_id = $this->_channelCache[$this->channel_id];
                 $this->_order[$this->order_sn]['channel_id'] = $this->channel_id;
             }else{
                 $this->addRowError($rowIndex, 'channel_id', "[{$this->channel_id}]不存在");
             }
         }else if(isset($this->_order[$this->order_sn]['channel_id'])) {
             $this->channel_id = $this->_order[$this->order_sn]['channel_id'];
         }
        if($this->follower_id) {
            $member = Member::find()->where(['username'=>$this->follower_id])->one();
            if($member) {
                $this->follower_id = $member->id;
            }else{
                $this->addRowError($rowIndex, 'follower_id', "[{$this->follower_id}]不存在");
            }
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
        //订单语言
        if($this->language) {
            $language = LanguageEnum::getValue($this->language,'getFlipMap');
            if(!$language) {
                $this->addRowError($rowIndex, 'language', "[{$this->language}]填写错误");
            }else{
                $this->language = $language;
            }
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
        //商品金额验证
        if($this->goods_price <=0) {            
            $this->addRowError($rowIndex, 'goods_price', "[{$this->goods_price}]填写错误，不能小于0");
        }
        //款号信息
        if($this->style_sn) {
            $style = Style::find()->where(['style_sn'=>$this->style_sn])->one();
            if(!$style) {
                $this->addRowError($rowIndex, 'style_sn', "[{$this->style_sn}]不存在");
            }else if($style->status != 1) {
                $this->addRowError($rowIndex, 'style_sn', "[{$this->style_sn}]不是启用状态");
            }else{
                $this->_style = $style;
            }            
        }
        //客户信息查询
        if(!($customer = $this->_customerCache[$this->order_sn]??false)) {
            //isNeedCheck：以订单号为唯一行，第一行需要验证，其他行不用验证
            if($this->customer_no) {            
                $customer = Customer::find()->where(['customer_no'=>$this->customer_no,'channel_id'=>$this->channel_id])->one();
                if(empty($customer)) {
                    $this->addRowError($rowIndex, 'customer_no', "[{$this->customer_no}]查询不到客户");
                }
            }else if($this->customer_email){
                $customer = Customer::find()->where(['email'=>$this->customer_email,'channel_id'=>$this->channel_id])->one();
                if(empty($customer)) {
                    $this->addRowError($rowIndex, 'customer_email', "[{$this->customer_email}]查询不到客户");
                }
            }else{                
                $this->addRowError($rowIndex, 'customer_no', "客户编号和客户邮箱 不能同时为空");
            }
            $this->_customerCache[$this->order_sn] = true;
        }
        $this->_customer = $customer;
        if($this->main_stone_price) {
            $this->goods_spec['主石金额'] = $this->main_stone_price;
        }
        if($this->second_stone_price1) {
            $this->goods_spec['副石1金额'] = $this->second_stone_price1;
        }
        if($this->stone_spec) {
            $this->goods_spec['石料规格'] = $this->stone_spec;
        }
        if($this->hasError() === false) {
            $this->loadOrder();
        }        
        return $this->hasError();
    }
    /**
     * 组装订单数据
     */
    private function loadOrder()
    {
        if(!isset($this->order_list[$this->order_sn])){
            $form = new OrderFullForm();
            $form->order = new Order();
            $form->order->language = $this->language;
            $form->order->currency = $this->currency;
            $form->order->sale_channel_id = $this->channel_id;
            $form->order->customer_id = $this->_customer->id;//客户id
            $form->order->customer_name = $this->_customer->realname;//客户姓名
            $form->order->customer_mobile = $this->_customer->mobile;//客户电话
            $form->order->customer_email = $this->_customer->email;//客户邮箱
            $form->order->follower_id = $this->follower_id;//销售人
            $form->order->remark = $this->remark;
            $form->order->order_time = $this->order_time;
            
            //订单金额
            $form->account = new OrderAccount();
            $form->account->other_fee = $this->other_fee;
            $form->account->order_amount = $this->order_amount;
            $form->account->paid_amount = $this->paid_amount;
            //收货地址
            $form->address = new OrderAddress();
            $form->address->realname = $this->_customer->realname;
            $form->address->mobile = $this->_customer->mobile;
            $form->address->country_id = $this->_customer->country_id;
            $form->address->province_id = $this->_customer->province_id;
            $form->address->city_id = $this->_customer->city_id;
            $form->address->zip_code = $this->_customer->zip_code;
            $form->address->address_details = $this->_customer->address;
            //发票信息
            $form->invoice = new OrderInvoice();
            $form->invoice->title_type = $this->_customer->invoice_title_type;
            $form->invoice->invoice_title = $this->_customer->invoice_title;
            $form->invoice->tax_number = $this->_customer->invoice_tax;
            $form->invoice->invoice_type = $this->_customer->invoice_type;
            $form->invoice->email = $this->_customer->invoice_email;
            $form->invoice->mobile = $this->_customer->mobile;
            $form->invoice->is_invoice = $this->_customer->is_invoice ? 1 : 0 ;
            
        }else{
            $form = $this->order_list[$this->order_sn];
        }
        //订单明细
        $goods = [
              'style_sn' => $this->style_sn,
              'goods_price'=>$this->goods_price,
              'goods_pay_price'=>$this->goods_price,
              'goods_num' =>$this->goods_num,
              'goods_name'=>$this->goods_name,
              'goods_spec'=>!empty($this->goods_spec)?json_encode($this->goods_spec):null,
                
        ];
        //商品属性
        $attrColumns = ArrayHelper::merge($this->attrInputColumns, $this->attrSelectColumns);
        foreach ($attrColumns as $attr) {
            $goods['goods_attrs'][] = $this->{$attr};
        }        
        $form->goods_list[] = $goods;
        
        $this->order_list[$this->order_sn] = $form;
    }
    /**
     * 订单校验
     */
    public function validateOrders()
    {   
        //订单金额校验
        $rowIndex = 3;
        foreach ($this->order_list ?? [] as $order) {
            if($order->account->order_amount > 0) {
                $goods_amount = 0;
                foreach ($order->goods_list as $goods){
                    $goods_amount += $goods['goods_pay_price'] * $goods['goods_num'];
                }                
                $other_fee = $order->account->other_fee/1;
                $paid_amount = $order->account->paid_amount/1;
                $_order_amount = $other_fee + $goods_amount;
                if($order->account->order_amount != $_order_amount) {
                    $this->addRowError($rowIndex, 'order_amount', "订单总金额({$order->account->order_amount})不对,系统计算总金额:{$_order_amount}(订单总金额=商品价格*商品数量+订单其它费用)");
                }else if($order->account->paid_amount > $_order_amount) {
                    $this->addRowError($rowIndex, 'paid_amount', "订单已付金额({$order->account->paid_amount})不能大于订单总金额({$_order_amount})");
                }
            }
            $rowIndex += count($order->goods_list);             
        }
    }
}
