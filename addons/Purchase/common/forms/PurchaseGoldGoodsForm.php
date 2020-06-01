<?php

namespace addons\Purchase\common\forms;

use Yii;

use addons\Purchase\common\models\PurchaseGoods;
use addons\Purchase\common\models\PurchaseGoodsAttribute;
use addons\Style\common\models\AttributeSpec;
use addons\Style\common\models\StyleAttribute;
use addons\Purchase\common\enums\PurchaseGoodsTypeEnum;
use addons\Style\common\enums\JintuoTypeEnum;
use common\enums\InputTypeEnum;
use common\enums\ConfirmEnum;
use common\helpers\ArrayHelper;

/**
 * 金料商品 Form
 *
 * @property string $attr_require 必填属性
 * @property string $attr_custom 选填属性
 */
class PurchaseGoldGoodsForm extends PurchaseGoodsForm
{
    public $style_cate_id = 14;//金料
    public $jintuo_type = JintuoTypeEnum::Chengpin;
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        //合并
        return ArrayHelper::merge(parent::attributeLabels() , [
                'attr_require'=>'当前属性',
                'attr_custom'=>'当前属性',
                'goods_sn'=>'商品编号',
                'style_cate_id'=>'商品分类',
                'cost_price'=>'金料总额',
                'gold_price'=>'金料单价/克',
        ]);
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
        return \Yii::$app->styleService->attribute->getAttrListByCateId($this->style_cate_id);
    }
    
}

