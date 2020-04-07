<?php

namespace addons\Style\common\forms;

use Yii;

use addons\Style\common\models\Style;

/**
 * 款式属性 Form
 *
 * @property string $attr_require 必填属性
 * @property string $attr_custom 选填属性
 */
class StyleAttrForm extends Style
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
        return [
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
              'attr_custom'=>'当前属性'
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
    
}
