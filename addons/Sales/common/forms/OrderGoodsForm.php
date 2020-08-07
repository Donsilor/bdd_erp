<?php

namespace addons\Sales\common\forms;

use addons\Sales\common\models\OrderGoods;
use addons\Sales\common\models\OrderGoodsAttribute;
use addons\Style\common\enums\QibanTypeEnum;
use addons\Style\common\models\AttributeSpec;
use addons\Supply\common\enums\PeiliaoTypeEnum;
use common\enums\ConfirmEnum;
use common\enums\InputTypeEnum;
use Yii;
use common\helpers\ArrayHelper;

/**
 * 订单 Form
 */
class OrderGoodsForm extends OrderGoods
{

    //属性必填字段
    public $attr_require;
    //属性非必填
    public $attr_custom;

    public $attr;

    public $style_id;

    public $cert_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [

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
        return ArrayHelper::merge(parent::rules() , $rules);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        //合并
        return ArrayHelper::merge(parent::attributeLabels() , [
                'attr_require'=>'当前属性',
                'attr_custom'=>'当前属性',
                'goods_sn' => '款号/起版号/批次号',
                'cert_id' => '证书号',
            ]);
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
        $attr_list = OrderGoodsAttribute::find()->select(['attr_id','if(attr_value_id=0,attr_value,attr_value_id) as attr_value'])->where(['id'=>$this->id])->asArray()->all();
        if(!empty($attr_list)) {
            $attr_list = array_column($attr_list,'attr_value','attr_id');
            $this->attr_custom  = $attr_list;
            $this->attr_require = $attr_list;
        }
        if($this->qiban_type == QibanTypeEnum::NON_VERSION){
            $this->goods_sn = $this->style_sn;
        }else{
            $this->goods_sn = $this->qiban_sn;
        }

    }
    /**
     * 初始化 已填写属性数据
     */
    public function initApplyEdit()
    {
        $attr_list = OrderGoodsAttribute::find()->select(['attr_id','if(attr_value_id=0,attr_value,attr_value_id) as attr_value'])->where(['id'=>$this->id])->asArray()->all();
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
        $attrs = OrderGoodsAttribute::find()->select(['attr_id','attr_value','if(attr_value_id=0,attr_value,attr_value_id) as attr_value2'])->where(['id'=>$this->id])->asArray()->all();
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
            $apply_info[$code] = ['label'=>$label,'value'=>$value,'org_value'=>$org_value,'changed'=>($value != $org_value)];
        }
        $this->apply_info = $apply_info;

    }
    /**
     * 创建商品属性
     */
    public function  createAttrs()
    {
        OrderGoodsAttribute::deleteAll(['id'=>$this->id]);
        $attr_info = [];
        foreach ($this->getPostAttrs() as $attr_id => $attr_value_id) {
            $spec = AttributeSpec::find()->where(['attr_id'=>$attr_id,'style_cate_id'=>$this->style_cate_id])->one();
            $model = new OrderGoodsAttribute();
            $model->id = $this->id;
            $model->attr_id  = $attr_id;
            if(InputTypeEnum::isText($spec->input_type)) {
                $model->attr_value_id  = 0;
                $model->attr_value = $attr_value_id;
            }else if(is_numeric($attr_value_id)){
                $attr_value = \Yii::$app->attr->valueName($attr_value_id);
                $model->attr_value_id  = $attr_value_id;
                $model->attr_value = $attr_value;
            }else{
                continue;
            }
            $model->sort = $spec->sort;

            //保存属性
            $attr_info[] = $model->attributes;

            if(false === $model->save()) {
                throw new \Exception($this->getErrors($model));
            }
        }

        //保存期货属性信息
        $this->attr_info = json_encode($attr_info);
        if(false === $this->save(true,['attr_info'])) {
            throw new \Exception($this->getErrors($this));
        }
    }
    /**
     * 采购商品申请编辑-创建
     */
    public function createApply()
    {
        //主要信息
        $fields = array('goods_name','goods_price','goods_num');
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
        $fields = [];
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



}
