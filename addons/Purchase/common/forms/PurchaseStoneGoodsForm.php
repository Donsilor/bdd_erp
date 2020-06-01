<?php

namespace addons\Purchase\common\forms;

use Yii;

use addons\Purchase\common\models\PurchaseGoods;
use addons\Style\common\models\AttributeSpec;
use addons\Style\common\models\StyleAttribute;
use addons\Purchase\common\enums\PurchaseGoodsTypeEnum;
use addons\Style\common\enums\JintuoTypeEnum;
use common\enums\InputTypeEnum;
use common\enums\ConfirmEnum;

/**
 * 石料商品 Form
 *
 * @property string $attr_require 必填属性
 * @property string $attr_custom 选填属性
 */
class PurchaseStoneGoodsForm extends PurchaseGoods
{
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
     * 采购商品申请编辑-创建
     */
    public function createApply()
    {
        //主要信息
        $fields = array('goods_name','cost_price','goods_num');
        $apply_info = array();
        foreach ($fields as $field) {
            $apply_info[] = array(
                    'code'=>$field,
                    'value'=>$this->$field,
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
        $fields = array('remark');
        foreach ($fields as $field) {
            $apply_info[] = array(
                    'code'=>$field,
                    'value'=>$this->$field,
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

