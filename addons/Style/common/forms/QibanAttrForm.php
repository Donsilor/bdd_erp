<?php

namespace addons\Style\common\forms;

use addons\Style\common\models\QibanAttribute;
use yii\base\Model;

/**
 * 款式编辑-款式属性 Form
 *
 * @property string $attr_require 必填属性
 * @property string $attr_custom 选填属性
 */
class QibanAttrForm extends Model
{
    //属性必填字段
    public $attr_require;
    //属性非必填
    public $attr_custom;
    
    public $qiban_id;
    
    public $style_cate_id;
    
    public $qiban_sn;
    
    public $is_combine;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
                [['qiban_id','style_cate_id','qiban_sn','is_combine'], 'required'],
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
        return [
              'attr_require'=>'当前属性',
              'attr_custom'=>'当前属性',
              'qiban_id'=>'款号id',
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
        $attr_list = QibanAttribute::find()->select(['attr_id','attr_values'])->where(['qiban_id'=>$this->qiban_id])->asArray()->all();
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
    
}
