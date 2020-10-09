<?php

namespace addons\Warehouse\common\forms;

use addons\Style\common\models\StoneStyle;
use addons\Warehouse\common\models\WarehouseStoneBillGoods;
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
class WarehouseStoneImportRkForm extends ImportForm
{
    public $file;

    //表格数据
    public $bill_id;
    public $bill_no;
    public $bill_type;
    public $stone_sn;
    public $style_sn;
    public $stone_name;
    public $stone_norms;
    public $stone_num;
    public $stone_type;
    public $stone_weight;
    public $stone_price;
    public $incl_tax_price;
    public $shape;
    public $color;
    public $clarity;
    public $cut;
    public $polish;
    public $symmetry;
    public $fluorescence;
    public $stone_colour;
    public $stone_size;
    public $cert_type;
    public $cert_id;
    public $remark;
    public $columns = [
            1=>'stone_sn',
            2=>'style_sn',
            3=>'stone_type',
            4=>'stone_name',
            5=>'stone_norms',
            6=>'stone_num',
            7=>'stone_weight',
            8=>'stone_price',
            9=>'incl_tax_price',
            10=>'shape',
            11=>'color',
            12=>'clarity',
            13=>'cut',
            14=>'polish',
            15=>'symmetry',
            16=>'fluorescence',
            17=>'stone_colour',
            18=>'stone_size',
            19=>'cert_type',
            20=>'cert_id',
            21=>'remark',
    ];
    //唯一行的字段
    public $uniqueKey = 'stone_sn';
    //只需要填写第一行的字段
    public $uniqueColumn = [
    ];
    public $requredColumns = [
            'style_sn',
            'stone_type',
            'stone_name',
            'shape',
            'stone_num',
            'stone_weight',
            'stone_price',
            'incl_tax_price',

    ];
    public $numberColumns = [
            'stone_num',
            'stone_weight',
            'stone_price',
            'incl_tax_price',

    ];
    //文本属性
    public $attrInputColumns = [
        AttrIdEnum::DIA_CERT_NO =>'cert_id',
    ];
    //单选下拉属性
    public $attrSelectColumns = [
            AttrIdEnum::MAIN_STONE_COLOR =>'color',
            AttrIdEnum::MAIN_STONE_CLARITY =>'clarity',
            AttrIdEnum::MAIN_STONE_CUT =>'cut',
            AttrIdEnum::MAIN_STONE_POLISH =>'polish',
            AttrIdEnum::MAIN_STONE_SYMMETRY =>'symmetry',
            AttrIdEnum::MAIN_STONE_FLUORESCENCE=>'fluorescence',
            AttrIdEnum::MAIN_STONE_COLOUR =>'stone_colour',
            AttrIdEnum::DIA_CERT_TYPE =>'cert_type',
    ];  

    
    
    public $goods_list;
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

        //石料单价验证
        if($this->stone_price <=0) {
            $this->addRowError($rowIndex, 'stone_price', "[{$this->stone_price}]填写错误，不能小于0");
        }
        //石料数量
        if($this->stone_num <=0) {
            $this->addRowError($rowIndex, 'stone_num', "[{$this->stone_num}]填写错误，不能小于0");
        }
        //石料重量
        if($this->stone_weight <=0) {
            $this->addRowError($rowIndex, 'stone_weight', "[{$this->stone_weight}]填写错误，不能小于0");
        }
        //石料重量
        if($this->incl_tax_price <=0) {
            $this->addRowError($rowIndex, 'incl_tax_price', "[{$this->incl_tax_price}]填写错误，不能小于0");
        }
        //款号信息
        if($this->style_sn) {
            $style = StoneStyle::find()->where(['style_sn'=>$this->style_sn])->one();
            if(!$style) {
                $this->addRowError($rowIndex, 'style_sn', "[{$this->style_sn}]不存在");
            }else if($style->status != 1) {
                $this->addRowError($rowIndex, 'style_sn', "[{$this->style_sn}]不是启用状态");
            }else{
                $this->_style = $style;
            }            
        }

        if($this->hasError() === false) {
            $this->loadWarehouseStoneGoods();
        }        
        return $this->hasError();
    }
    /**
     * 组装数据
     */
    private function loadWarehouseStoneGoods()
    {
        if(!isset($this->goods_list[$this->stone_sn])){
            $form = new WarehouseStoneBillGoods();
            $form->bill_id = $this->bill_id;
            $form->bill_sn = $this->bill_sn;
            $form->bill_type = $this->bill_type;
            $form->stone_sn = $this->stone_sn;
            $form->style_sn = $this->style_sn;
            $form->stone_name = $this->stone_name;
            $form->stone_norms = $this->stone_norms;
            $form->stone_num = $this->stone_num;
            $form->stone_weight = $this->stone_weight;
            $form->stone_price = $this->stone_price;

            foreach ($this->attrSelectColumns as $attr_id => $attr_name){
                $valueList = \Yii::$app->attr->valueMap($attr_id);
                $valueList = array_flip($valueList);
                $attr_value_id = isset($valueList[$this->$attr_name]) ? $valueList[$this->$attr_name] : "";
                $form->$attr_name =  (string)$attr_value_id ?? "";
            }
//            $form->color = $this->color;
//            $form->clarity = $this->clarity;
//            $form->cut = $this->cut;
//            $form->polish = $this->polish;
//            $form->symmetry = $this->symmetry;
//            $form->fluorescence = $this->fluorescence;
//            $form->stone_colour = $this->stone_colour;
//            $form->cert_type = $this->cert_type;

            $form->cert_id = $this->cert_id;

        }else{
            $form = $this->goods_list[$this->stone_sn];
        }
        $this->goods_list[$this->stone_sn] = $form;
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
