<?php

namespace addons\Purchase\common\forms;

use addons\Style\common\enums\AttrIdEnum;
use addons\Supply\common\enums\PeijianTypeEnum;
use addons\Supply\common\enums\PeishiTypeEnum;
use addons\Supply\common\enums\TempletTypeEnum;
use addons\Warehouse\common\enums\PeiJianWayEnum;
use addons\Warehouse\common\enums\PeiLiaoWayEnum;
use addons\Warehouse\common\enums\PeiShiWayEnum;
use Yii;
use addons\Style\common\enums\QibanTypeEnum;
use addons\Purchase\common\models\PurchaseGoods;
use addons\Purchase\common\models\PurchaseGoodsAttribute;
use addons\Style\common\models\AttributeSpec;
use addons\Style\common\models\StyleAttribute;
use addons\Style\common\enums\JintuoTypeEnum;
use common\enums\InputTypeEnum;
use common\enums\ConfirmEnum;
use addons\Supply\common\enums\PeiliaoTypeEnum;
use addons\Style\common\enums\AttrModuleEnum;

/**
 * 款式编辑-款式属性 Form
 *
 * @property string $attr_require 必填属性
 * @property string $attr_custom 选填属性
 */
class PurchaseGoodsForm extends PurchaseGoods
{
    //属性必填字段
    public $attr_require;
    //属性非必填
    public $attr_custom;

    public $attr;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {      
         $rules = [
            [['peiliao_type','peishi_type', 'peijian_type', 'templet_type'], 'required'],
            [['attr_require'], 'required','isEmpty'=>function($value){
                if(!empty($value)) {
                    foreach ($value as $k=>$v) {
                        if($v === "") {
                            $name = \Yii::$app->attr->attrName($k);
                            $this->addError("attr_require[{$k}]","[{$name}]不能为空");
                            return true;
                        }
                    }
                    return false;
                }
                return false;
            }],
            [['attr_require','attr_custom'],'getPostAttrs'],
         ];
         return array_merge(parent::rules() , $rules);
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {   
        //合并
        return parent::attributeLabels() + [
                'attr_require'=>'当前属性',
                'attr_custom'=>'当前属性',                
              ];
    }
    /**
     * 款式基础属性
     */
    public function getPostAttrs()
    {
        $attr_list = [];
        if(!empty($this->attr_require)){
            $attr_list =  $this->attr_require + $attr_list;
        }
        if(!empty($this->attr_custom)){
            $attr_list =  $this->attr_custom + $attr_list;
        }
        return $attr_list;
    }
    /**
     * 初始化 已填写属性数据
     */
    public function initAttrs()
    {
        $attr_list = PurchaseGoodsAttribute::find()->select(['attr_id','if(attr_value_id=0,attr_value,attr_value_id) as attr_value'])->where(['id'=>$this->id])->asArray()->all();
        if(!empty($attr_list)) {
            $attr_list = array_column($attr_list,'attr_value','attr_id'); 
            $this->attr_custom  = $attr_list;
            $this->attr_require = $attr_list; 
        }        
    } 
    /**
     * 初始化 已填写属性数据
     */
    public function initApplyEdit()
    {
        $attr_list = PurchaseGoodsAttribute::find()->select(['attr_id','if(attr_value_id=0,attr_value,attr_value_id) as attr_value'])->where(['id'=>$this->id])->asArray()->all();
        if(!empty($attr_list)) {
            $attr_list = array_column($attr_list,'attr_value','attr_id');
        }
        if($this->is_apply == 0) {
            $this->apply_info = [];
        }else if(!is_array($this->apply_info)) {
            $this->apply_info  = json_decode($this->apply_info,true) ?? [];
        }

        //$apply_info = [];
        foreach ($this->apply_info as $k=>$item) {
            $group = $item['group'];
            $code  = $item['code'];            
            $label = $item['label'];
            $value = $item['value'];
            if($group == 'base') {
               // $org_value = $this->$code;
                $this->$code = $value;               
            }else if($group == 'attr'){
                $value = $item['value_id'];
                //$org_value = $attr_list[$code]??'';
                $attr_list[$code] = $value;
            }
            //$apply_info[$code] = ['label'=>$label,'value'=>$value,'changed'=>($value != $org_value)];
        }
        //$this->apply_info = $apply_info;
        $this->attr_custom  = $attr_list;
        $this->attr_require = $attr_list;
        
    } 
    /**
     * 初始化 申请表单数据
     */
    public function initApplyView()
    {
        $apply_info = array();
        if(!$this->apply_info) {
            return ;
        }        
        $attrs = PurchaseGoodsAttribute::find()->select(['attr_id','attr_value','if(attr_value_id=0,attr_value,attr_value_id) as attr_value2'])->where(['id'=>$this->id])->asArray()->all();
        $attrs = array_column($attrs,'attr_value','attr_id');
        
        $this->apply_info  = json_decode($this->apply_info,true) ?? [];

        foreach ($this->apply_info as $k=>$item) {
            $group = $item['group'];
            $code  = $item['code'];
            $value = $item['value'];
            $label = $item['label'];
            if($group == 'base') {
                $org_value = $this->$code;                
            }else if($group == 'attr'){
                $org_value= $attrs[$code] ?? '';
            }else {
                $org_value = '';
            }
            if($code == 'peiliao_type') {
                $org_value = PeiliaoTypeEnum::getValue($org_value);
                $value = PeiliaoTypeEnum::getValue($value);
            }
            if($code == 'peishi_type') {
                $org_value = PeishiTypeEnum::getValue($org_value);
                $value = PeishiTypeEnum::getValue($value);
            }
            if($code == 'peijian_type') {
                $org_value = PeijianTypeEnum::getValue($org_value);
                $value = PeijianTypeEnum::getValue($value);
            }
            if($code == 'templet_type') {
                $org_value = TempletTypeEnum::getValue($org_value);
                $value = PeijianTypeEnum::getValue($value);
            }
            $apply_info[$code] = ['label'=>$label,'value'=>$value,'org_value'=>$org_value,'changed'=>($value != $org_value)];
        }
        $this->apply_info = $apply_info;
        
    }
    /**
     * 创建商品属性
     */
    public function  createAttrs()
    {  
        PurchaseGoodsAttribute::deleteAll(['id'=>$this->id]);   
        foreach ($this->getPostAttrs() as $attr_id => $attr_value_id) {            
            $spec = AttributeSpec::find()->where(['attr_id'=>$attr_id,'style_cate_id'=>$this->style_cate_id])->one();
            $model = new PurchaseGoodsAttribute();
            $model->id = $this->id;
            $model->attr_id  = $attr_id; 

            if(InputTypeEnum::isText($spec->input_type)) {
                $model->attr_value_id  = 0;
                $model->attr_value = $attr_value_id;
            }else if(is_numeric($attr_value_id)){
                $attr_value = \Yii::$app->attr->valueName($attr_value_id);
                $model->attr_value_id  = $attr_value_id; 
                $model->attr_value = $attr_value;
                /* $pices = explode('-',$attr_value);
                if(count($pices)==2) {
                    if(is_numeric($pices[0]) && is_numeric($pices[1])) {
                        $model->attr_value_min = $pices[0];
                        $model->attr_value_max = $pices[1];
                    }
                } */
            }else{
                continue;
            }   
            $model->sort = $spec->sort;
            if(false === $model->save()) {
                throw new \Exception($this->getErrors($model));
            }
        }
    }
    /**
     * 采购商品申请编辑-创建
     */
    public function createApply()
    {
        //主要信息
        $fields = array('goods_name','cost_price','goods_num','peiliao_type','peishi_type','peijian_type','templet_type');
        $apply_info = array();
        foreach ($fields as $field) {
            $apply_info[] = array(
                    'code'=>$field,
                    'value'=>$this->$field ??'',
                    'label'=>$this->getAttributeLabel($field),
                    'group'=>'base',
            );
        }
        //属性信息
        foreach ($this->getPostAttrs() as $attr_id => $attr_value_id) {
            $spec = AttributeSpec::find()->where(['attr_id'=>$attr_id,'style_cate_id'=>$this->style_cate_id])->one();
            
            if(InputTypeEnum::isText($spec->input_type)) {
                $value_id = 0;
                $value = $attr_value_id;
            }else if(is_numeric($attr_value_id)){
                $value_id = $attr_value_id;
                $value = Yii::$app->attr->valueName($attr_value_id);
            }else{
                $value_id = null;
                $value = null;
            }
            $apply_info[] = array(
                    'code' => $attr_id,
                    'value' => $value,
                    'value_id'=>$attr_value_id,
                    'label' => Yii::$app->attr->attrName($attr_id),
                    'group' =>'attr',
             );            
        }
        //其他信息
        $fields = array(
                'gold_price','gold_cost_price','gold_amount','gross_weight',
                'gold_loss','single_stone_weight','parts_material','parts_num','parts_weight','parts_price','parts_amount','suttle_weight',
                'peishi_fee','peishi_amount','xianqian_price','factory_cost_price','factory_mo','parts_price','factory_cost_price','factory_mo',
                'jiagong_fee','gong_fee','biaomiangongyi_fee','fense_fee','bukou_fee','penrasa_fee','edition_fee','gaitu_fee',
                'penla_fee','parts_fee','cert_fee','unit_cost_price','factory_total_price','company_total_price','stone_info','parts_remark',
                'remark','main_stone_sn','second_stone_sn1','second_stone_sn2','ke_gong_fee','peiliao_way','peijian_way','main_peishi_way','second_peishi_way1','second_peishi_way2'
        );
        foreach ($fields as $field) {
            $apply_info[] = array(
                    'code'=>$field,
                    'value'=>$this->$field ?? '',
                    'label'=>$this->getAttributeLabel($field),
                    'group'=>'base',
            );
        }
        $this->is_apply   = ConfirmEnum::YES;
        $this->apply_info = json_encode($apply_info);
        if(false === $this->save(true,['is_apply','apply_info','updated_at'])) {
            throw new \Exception("保存失败",500);
        }
        
    }


    /***
     * 计算各种费用
     */
    public function setComputeFee(){

        //【镶石费=镶石单价*总副石数量】
        $second_stone_num = 0;
        $atts = $this->getPostAttrs();
        if(isset($atts[AttrIdEnum::SIDE_STONE1_NUM]) && !empty($atts[AttrIdEnum::SIDE_STONE1_NUM])){
            $second_stone_num += $atts[AttrIdEnum::SIDE_STONE1_NUM];
        }
        if(isset($atts[AttrIdEnum::SIDE_STONE2_NUM]) && !empty($atts[AttrIdEnum::SIDE_STONE2_NUM])){
            $second_stone_num += $atts[AttrIdEnum::SIDE_STONE2_NUM];
        }
        $this->xiangqian_fee = $this->xianqian_price * $second_stone_num;

        //含耗重=净重*（1+损耗）
        $this->gross_weight = $this->suttle_weight * (1 + $this->gold_loss);

        //金料成本=金价*净重*（1+损耗）
        $this->gold_amount = $this->gross_weight * $this->gold_price;

        //【配件额=配件重*配件金价】
        $this->parts_amount = $this->parts_weight * $this->parts_price;

        //工费【需要自动计算】=克工费*含耗重
        $this->gong_fee = $this->gross_weight * $this->ke_gong_fee;

        //配石费 = 配石工费 * 配石重量
        $this->peishi_amount = $this->peishi_weight * $this->peishi_fee;

        //总工费【自动计算】=所有工费【基本工费+配件工费+配石工费+镶石费+表面工艺费+分色费+喷砂费+补口工费+版费 + 证书费 + 其他费用】
        $this->total_gong_fee = $this->gong_fee + $this->parts_fee + $this->peishi_amount + $this->xiangqian_fee + $this->biaomiangongyi_fee
            + $this->fense_fee + $this->penrasa_fee + $this->bukou_fee + $this->edition_fee + $this->cert_fee + $this->other_fee;



        //主石成本 = 主石重 * 主石买入单价
        $main_stone_price = $atts[AttrIdEnum::MAIN_STONE_PRICE] ?? 0;
        $main_stone_weight = $atts[AttrIdEnum::MAIN_STONE_WEIGHT] ?? 0;
        $main_stone_weight = $main_stone_weight == ''? 0:$main_stone_weight;
        $this->main_stone_cost = round($main_stone_weight * $main_stone_price,2);

        //副石1成本 = 副石1重 * 副石1买入单价
        $side_stone1_price = $atts[AttrIdEnum::SIDE_STONE1_PRICE] ?? 0;
        $side_stone1_weight = $atts[AttrIdEnum::SIDE_STONE1_WEIGHT] ?? 0;
        $side_stone1_weight = $side_stone1_weight == ''? 0:$side_stone1_weight;
        $this->second_stone1_cost = round($side_stone1_weight * $side_stone1_price,2);

        //副石2成本 = 副石2重 * 副石2买入单价
        $side_stone2_price = $atts[AttrIdEnum::SIDE_STONE2_PRICE] ?? 0;
        $side_stone2_weight = $atts[AttrIdEnum::SIDE_STONE2_WEIGHT] ?? 0;
        $side_stone2_weight = $side_stone2_weight == ''? 0:$side_stone2_weight;
        $this->second_stone2_cost = round($side_stone2_weight * $side_stone2_price,2);

        //公司成本 = 金料成本 + 主石成本 + 副石1成本 + 副石2成本 + 配件额 + 总工费
        $this->cost_price = $this->gold_amount + $this->main_stone_cost + $this->second_stone1_cost +
            $this->second_stone2_cost + $this->parts_amount + $this->total_gong_fee ;

        $this->company_total_price = $this->cost_price * $this->goods_num;

        //工厂成本
        $this->factory_cost_price = 0;
        if($this->main_peishi_way == PeiShiWayEnum::FACTORY){
            $this->factory_cost_price += $this->main_stone_cost;
        }
        if($this->second_peishi_way1 == PeiShiWayEnum::FACTORY){
            $this->factory_cost_price += $this->second_stone1_cost;
        }
        if($this->second_peishi_way2 == PeiShiWayEnum::FACTORY){
            $this->factory_cost_price += $this->second_stone2_cost;
        }
        if($this->peiliao_way == PeiLiaoWayEnum::FACTORY){
            $this->factory_cost_price += $this->gold_amount;
        }
        if($this->peijian_way == PeiJianWayEnum::FACTORY){
            $this->factory_cost_price += $this->parts_amount;
        }

        $this->factory_total_price = $this->factory_cost_price * $this->goods_num;



    }
   
}
