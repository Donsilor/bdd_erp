<?php

namespace addons\Style\common\forms;

use Yii;

use addons\Style\common\models\Style;
use addons\Style\common\models\StyleAttribute;
use yii\base\Model;
use addons\Style\common\models\AttributeSpec;
use common\enums\StatusEnum;
use common\enums\InputTypeEnum;
use addons\Style\common\enums\AttrModuleEnum;

/**
 * 款式编辑-款式属性 Form
 *
 * @property string $attr_require 必填属性
 * @property string $attr_custom 选填属性
 */
class StyleAttrForm extends Model
{
    //属性必填字段
    public $attr_require;
    //属性非必填
    public $attr_custom;
    
    public $style_id;
    
    public $style_cate_id;
    
    public $style_sn;
    
    public $tuo_type;//金托类型
    public $is_inlay;//是否镶嵌
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
                [['style_id','style_cate_id','style_sn'], 'required'],
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
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        
        return  [
              'attr_require'=>'当前属性',
              'attr_custom'=>'当前属性',
              'style_id'=>'款号id',
              'style_cate_id'=>'款式分类id'
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
        $models = StyleAttribute::find()->select(['input_type','attr_id','attr_values'])->where(['style_id'=>$this->style_id])->all();
        if(empty($models)) {
            return ;
        }
        $attr_list = [];
        foreach ($models as $model){
            $attr_values = $model->attr_values;
            if($model->input_type != InputTypeEnum::INPUT_TEXT) {
                $split_values = explode(",",$attr_values);
                if(count($split_values) > 1) {
                    $attr_values = $split_values;
                }
            }
            $attr_list[$model->attr_id] = $attr_values;
        }        
        $this->attr_custom  = $attr_list;
        $this->attr_require = $attr_list;
    }
    
    /**
     * 创建 款式属性关联
     * @param unknown $style_id
     * @param array $attr_list
     */
    public function createAttrs()
    {   
        //批量删除
        StyleAttribute::deleteAll(['style_id'=>$this->style_id]);
        foreach ($this->getPostAttrs() as $attr_id => $attr_value) {
            $spec = AttributeSpec::find()->where(['attr_id'=>$attr_id,'style_cate_id'=>$this->style_cate_id])->one();
            $model = StyleAttribute::find()->where(['style_id'=>$this->style_id,'attr_id'=>$attr_id])->one();
            if(!$model) {
                $model = new StyleAttribute();
                $model->style_id = $this->style_id;
                $model->attr_id  = $attr_id;
            }
            $model->is_require = $spec->is_require;
            $model->input_type = $spec->input_type;
            $model->attr_type = $spec->attr_type;
            $model->is_inlay  = $spec->is_inlay;
            $model->sort = $spec->sort;
            $model->attr_values = is_array($attr_value) ? implode(',',$attr_value) : $attr_value;
            $model->status = StatusEnum::ENABLED;
            $model->save();
        }        
    }
    
    /**
     * 获取款式属性列表
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getAttrList($module = null)
    {   
        return \Yii::$app->styleService->attribute->module($module)->getAttrTypeListByCateId($this->style_cate_id,null,$this->is_inlay);
    }
    
}
