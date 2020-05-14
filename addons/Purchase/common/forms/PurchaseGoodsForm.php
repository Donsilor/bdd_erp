<?php

namespace addons\Purchase\common\forms;

use Yii;

use addons\Purchase\common\models\PurchaseGoods;
use addons\Purchase\common\models\PurchaseGoodsAttribute;
use addons\Style\common\models\AttributeSpec;
use addons\Style\common\models\StyleAttribute;
use addons\Purchase\common\enums\PurchaseGoodsTypeEnum;
use addons\Style\common\enums\JintuoTypeEnum;
use addons\Style\common\enums\AttrTypeEnum;
use common\enums\InputTypeEnum;

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
     * 自动填充已填写 表单属性
     */
    public function initAttrs()
    {
        $attr_list = PurchaseGoodsAttribute::find()->select(['attr_id','if(attr_value_id=0,attr_value,attr_value_id) as attr_value'])->where(['id'=>$this->id])->asArray()->all();
        if(empty($attr_list)) {
            return ;
        }
        $attr_list = array_column($attr_list,'attr_value','attr_id');        
        $this->attr_custom  = $attr_list;
        $this->attr_require = $attr_list;
    } 
    /**
     * 创建商品属性
     */
    public function createAttrs()
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
                $pices = explode('-',$attr_value);
                if(count($pices)==2) {
                    if(is_numeric($pices[0]) && is_numeric($pices[1])) {
                        $model->attr_value_min = $pices[0];
                        $model->attr_value_max = $pices[1];
                    }
                }
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
     * 获取款式属性列表
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getAttrList()
    {
        $attr_type = JintuoTypeEnum::getValue($this->jintuo_type,'getAttrTypeMap');
        if($this->goods_type == PurchaseGoodsTypeEnum::STYLE) {
            $attr_list = \Yii::$app->styleService->styleAttribute->getStyleAttrList($this->style_id, $attr_type);
        }else{
            $attr_list = \Yii::$app->styleService->qibanAttribute->getQibanAttrList($this->style_id, $attr_type);
        }
        return $attr_list;
    }
   
}
