<?php

namespace addons\Purchase\common\forms;

use Yii;

use addons\Purchase\common\models\PurchaseGoods;
use addons\Purchase\common\models\PurchaseGoodsAttribute;

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
    public $is_combine;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {      
         return parent::rules() + [
            [['attr_require'], 'required','isEmpty'=>function($value){
                return false;
            }],
            [['attr_require','attr_custom'],'getPostAttrs'],
         ];
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
     * 自动填充已填写 表单属性
     */
    public function initAttrs()
    {
        $attr_list = PurchaseGoodsAttribute::find()->select(['attr_id','attr_value'])->where(['id'=>$this->id])->asArray()->all();
        if(empty($attr_list)) {
            return ;
        }
        $attr_list = array_column($attr_list,'attr_value','attr_id');
        foreach ($attr_list as $attr_id => & $attr_value) {
            $split_value = explode(",",$attr_value);
            if(count($split_value) > 1) {
                $attr_value = $split_value;
            }
        }
        $this->attr_custom  = $attr_list;
        $this->attr_require = $attr_list;
    } 
    /**
     * 创建商品属性
     */
    public function createGoodsAttribute()
    {
        $attr_list = $this->getPostAttrs(); 
        PurchaseGoodsAttribute::deleteAll(['id'=>$this->id]);        
        foreach ($attr_list as $attr_id => $attr_value) {
            $model = PurchaseGoodsAttribute::find()->where(['id'=>$this->id,'attr_id'=>$attr_id])->one();
            if(!$model) {
                $model = new PurchaseGoodsAttribute();
                $model->id = $this->id;
                $model->attr_id  = $attr_id;
            }
            $model->attr_value = $attr_value;
            $model->save();
        }
    }
   
}
