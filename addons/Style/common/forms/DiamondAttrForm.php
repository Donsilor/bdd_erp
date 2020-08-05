<?php

namespace addons\Style\common\forms;

use addons\Style\common\enums\JintuoTypeEnum;
use addons\Style\common\enums\QibanTypeEnum;
use addons\Style\common\models\Diamond;
use addons\Style\common\models\AttributeSpec;
use addons\Style\common\models\QibanAttribute;
use addons\Style\common\enums\AttrModuleEnum;
use common\enums\AuditStatusEnum;
use common\enums\InputTypeEnum;
use common\enums\StatusEnum;


/**
 * 款式编辑-款式属性 Form
 *
 * @property string $attr_require 必填属性
 * @property string $attr_custom 选填属性
 */
class DiamondAttrForm extends Diamond
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
        $diamond = Diamond::find()->where(['cert_id'=>$this->cert_id, 'status'=>StatusEnum::ENABLED])->one();
        $attr_list_l = \Yii::$app->styleService->diamond->getMapping();
        $attr_list = [];
        foreach ($attr_list_l as $attr){
            $attr_list[$attr['attr_id']] = $diamond->{$attr['attr_field']};

        }
        if(!empty($attr_list)) {
            $this->attr_custom  = $attr_list;
            $this->attr_require = $attr_list;
        }
    }




}
