<?php

namespace addons\Sales\common\forms;

use Yii;
use common\helpers\ArrayHelper;
use addons\Sales\common\models\Order;
use addons\Sales\common\models\OrderGoods;
use addons\Style\common\models\Style;
use common\enums\StatusEnum;
use addons\Style\common\enums\JintuoTypeEnum;
use common\enums\TargetTypeEnum;

/**
 * 订单 Form
 */
class ExternalOrderForm extends Order
{
    //审批流程
    public $targetType;
    
    public $consignee_id;
    public $goods_list;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
                [['out_trade_no','consignee_id'],'required'], 
                [['out_trade_no'],'unique', 'targetAttribute'=>['out_trade_no'],
                     'message' => "当前外部订单号已被使用了" //错误信息
                ],
                ['goods_list','validateGoodsList']
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
             //'out_trade_no'=>'平台订单号',
             'consignee_id'=>'收货地址'
        ]);
    } 
    /**
     * 校验商品和封装商品数据
     * @param unknown $attribute
     */
    public function validateGoodsList($attribute)
    {    
        
        foreach ($this->goods_list ?? [] as $k=>$goods){
             
             $model = new OrderGoods();
             $model->attributes = $goods;
             
             $errLine = "[行".($k+1)."]"; 
             if(!($model->style_sn = trim($model->style_sn))) {
                 $this->addError($attribute,$errLine.'款号不能为空');
                 return ;
             }
             if($model->goods_name == '') {
                 $this->addError($attribute,$errLine.'商品名称不能为空');
                 return ;
             }
             if($model->goods_pay_price == '') {
                 $this->addError($attribute,$errLine.'商品价格不能为空');
                 return ;
             }
             if(!is_numeric($model->goods_pay_price) || $model->goods_pay_price <0) {
                 $this->addError($attribute,$errLine.'商品价格不合法');
                 return ;
             }
             
             $style = Style::find()->where(['style_sn'=>$model->style_sn,'status'=>StatusEnum::ENABLED])->one();
             if(empty($style)) {
                 $this->addError($attribute,$errLine.'款号不存在');
                 return ;
             }
             if($model->goods_spec) {
                 $model->goods_spec = json_encode(['尺寸'=>$model->goods_spec]);
             }
             $model->goods_num = 1; 
             $model->goods_price = $model->goods_pay_price;
             $model->jintuo_type = JintuoTypeEnum::Chengpin;
             $model->style_cate_id = $style->style_cate_id;
             $model->product_type_id = $style->product_type_id;
             $model->is_inlay = $style->is_inlay;
             $model->style_channel_id = $style->style_channel_id;
             $model->style_sex = $style->style_sex;
             $model->goods_image = $style->style_image;
             $this->goods_list[$k] = $model->toArray();
        }       
    }
    /**
     * 收货地址 列表
     * @return array
     */
    public function getConsigneeMap()
    {   
        $map = self::getConsigneeList();
        return array_column(self::getConsigneeList(), 'title','_id');
    }
    /**
     * 收货信息配置
     * @return array
     */
    public static function getConsigneeList()
    {
        return [
                1 => [ 
                     '_id'=>1, 
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
     * 获取当前提交的 订单收货信息
     */
    public function getConsigneeInfo()
    {
         return self::getConsigneeList()[$this->consignee_id] ?? [];
    } 
    
    public function getTargetType(){
        switch ($this->sale_channel_id){
            case 3:
                $this->targetType = TargetTypeEnum::ORDER_F_MENT;
                break;
            case 4:
                $this->targetType = TargetTypeEnum::ORDER_Z_MENT;
                break;
            case 9:
                $this->targetType = TargetTypeEnum::ORDER_T_MENT;
                break;
            default:
                $this->targetType = false;
                
        }
        
        return $this->targetType;
    }
}
