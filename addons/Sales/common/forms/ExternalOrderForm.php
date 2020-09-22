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
    public $other_fee;
    public $arrive_amount;
    public $goods_list;
    public $_platform;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
                [['platform_id','out_trade_no'],'required'], 
                [['other_fee','arrive_amount'], 'number'],
                [['out_trade_no'],'unique', 'targetAttribute'=>['out_trade_no'],
                     'message' => "当前外部订单号已被使用了" //错误信息
                ],
                ['goods_list','validateGoodsList']
        ];
        $rules = ArrayHelper::merge($rules,parent::rules());
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
             'platform_id'=>'销售平台',
             'order_time' => '下单/支付时间',
             'pay_time' => '下单/支付时间',
             'other_fee' => '订单其它费用',
             'arrive_amount' => '订单到账金额'
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
             
             $errLine = "[商品".($k+1)."]"; 
             if(!($model->style_sn = trim($model->style_sn))) {
                 $this->addError($attribute,$errLine.'款号不能为空');
                 return ;
             }
             if($model->goods_name == '') {
                 $this->addError($attribute,$errLine.'商品名称不能为空');
                 return ;
             }
             if($model->goods_price == '') {
                 $this->addError($attribute,$errLine.'商品价格不能为空');
                 return ;
             }
             if(!is_numeric($model->goods_price) || $model->goods_price <0) {
                 $this->addError($attribute,$errLine.'商品价格不合法');
                 return ;
             }
             
             $style = Style::find()->where(['style_sn'=>$model->style_sn,'status'=>StatusEnum::ENABLED])->one();
             if(empty($style)) {
                 $this->addError($attribute,$errLine.'款号不存在');
                 return ;
             }
             $goods_spec = [];
             if(!empty($goods['size'])) {
                 $goods_spec['尺寸(cm)'] = $goods['size'];
             }
             if(!empty($goods['finger'])) {
                 if(empty($goods['finger_type'])) {
                     $this->addError($attribute,$errLine.'手寸类型不能为空');
                     return ;
                 }
                 $goods_spec['手寸'] = ($goods['finger_type']??'').'#'.(trim($goods['finger']??'','#'));
             }
             $model->goods_spec = $goods_spec ? json_encode($goods_spec) : null;
             $model->goods_num = 1; 
             $model->goods_pay_price = $model->goods_price;
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
