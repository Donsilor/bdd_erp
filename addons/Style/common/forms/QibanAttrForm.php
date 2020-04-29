<?php

namespace addons\Style\common\forms;

use addons\Style\common\enums\JintuoTypeEnum;
use addons\Style\common\enums\QibanTypeEnum;
use addons\Style\common\models\Qiban;
use addons\Style\common\models\AttributeSpec;
use addons\Style\common\models\QibanAttribute;


/**
 * 款式编辑-款式属性 Form
 *
 * @property string $attr_require 必填属性
 * @property string $attr_custom 选填属性
 */
class QibanAttrForm extends Qiban
{
    //属性必填字段
    public $attr_require;
    //属性非必填
    public $attr_custom;
    public $style_id;
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
        $attr_list = QibanAttribute::find()->select(['attr_id','attr_values'])->where(['qiban_id'=>$this->id])->asArray()->all();
        if(empty($attr_list)) {

            return ;
        }
        $attr_list = array_column($attr_list,'attr_values','attr_id');
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
    public function createAttrs()
    {
        QibanAttribute::deleteAll(['qiban_id'=>$this->id]);
        foreach ($this->getPostAttrs() as $attr_id => $attr_value) {
            $spec = AttributeSpec::find()->where(['attr_id'=>$attr_id,'style_cate_id'=>$this->style_cate_id])->one();
            $model = QibanAttribute::find()->where(['qiban_id'=>$this->id,'attr_id'=>$attr_id])->one();
            if(!$model) {
                $model = new QibanAttribute();
                $model->qiban_id = $this->id;
                $model->attr_id  = $attr_id;
            }
            $model->is_require = $spec->is_require;
            $model->input_type = $spec->input_type;
            $model->attr_type  = $spec->attr_type;
            $model->attr_values = is_array($attr_value) ? implode(',',$attr_value) : $attr_value;
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
        $attr_list = \Yii::$app->styleService->styleAttribute->getStyleAttrList($this->style_id, $attr_type);
        return $attr_list;
    }

}
