<?php

namespace addons\Style\common\forms;

use Yii;

use addons\Style\common\models\Style;
use addons\Style\common\models\StyleAttribute;
use yii\base\Model;
use addons\Style\common\enums\AttrTypeEnum;

/**
 * 款式属性 Form
 *
 * @property string $attr_require 必填属性
 * @property string $attr_custom 选填属性
 */
class StyleGoodsForm extends Model
{
    //选中属性值列表
    public $style_spec;

    public $style_id;
    
    public $style_cate_id;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
                [['style_id','style_cate_id'], 'required'],
                [['style_spec'],'getPostAttrs'],];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
                'style_spec'=>'规格属性',
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
        $attr_list = StyleAttribute::find()->select(['attr_id','attr_values'])->where(['style_id'=>$this->style_id,'attr_type'=>AttrTypeEnum::TYPE_SALE])->asArray()->all();
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
        $style_spec['a'] = $attr_list;
        $this->attr_spec_a  = $attr_list;
    }
    
}
