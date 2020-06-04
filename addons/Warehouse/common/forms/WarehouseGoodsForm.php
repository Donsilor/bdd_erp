<?php

namespace addons\Warehouse\common\forms;

use addons\Warehouse\common\models\WarehouseGoods;
use common\enums\ConfirmEnum;
use common\helpers\ArrayHelper;

/**
 * 维修退货单 Form
 *
 */
class WarehouseGoodsForm extends WarehouseGoods
{
    public $goods_ids;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {      
         $rules = [
            [[], 'required']
         ];
         return array_merge(parent::rules() , $rules);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        //合并
        return ArrayHelper::merge(parent::attributeLabels() , [

        ]);
    }

    /**
     * 初始化 已填写属性数据
     */
    public function initApplyEdit()
    {
        if($this->is_apply == ConfirmEnum::NO) {
            $this->apply_info = [];
        }else if(!is_array($this->apply_info)) {
            $this->apply_info  = json_decode($this->apply_info,true) ?? [];
        }
        foreach ($this->apply_info as $k=>$val) {
            $this->$k = $val;
        }
        $this->apply_info = json_encode($this->apply_info);

    }

    /**
     * 初始化 申请表单数据
     */
    public function initApplyView()
    {
        if(!$this->apply_info) {
            return ;
        }

        $this->apply_info  = json_decode($this->apply_info,true) ?? [];
        $attr_file = ['finger','cert_type','main_stone_type','diamond_shape','diamond_color','diamond_clarity',
            'diamond_cut','diamond_polish','diamond_symmetry','diamond_fluorescence','second_stone_type1',
            'second_stone_type2'];
        foreach ($this->apply_info as $k=>$val) {
            $label = $this->getAttributeLabel($k);
            $org_value = $this->$k;
            if(in_array($k,$attr_file)){
                $val = \Yii::$app->attr->valueName($val);
                $org_value = \Yii::$app->attr->valueName($org_value);
            }
            $apply_info[] = ['label'=>$label,'value'=>$val,'org_value'=>$org_value,'changed'=>($val != $org_value)];
        }
        $this->apply_info = $apply_info;
    }


    public function createApply(){
        $data = \Yii::$app->request->post('WarehouseGoodsForm');
        $this->apply_id = \Yii::$app->user->identity->getId();
        $this->apply_info = json_encode($data);
        $this->is_apply   = ConfirmEnum::YES;
        if(false === $this->save(true,['is_apply','apply_info'])) {
            throw new \Exception("保存失败",500);
        }

    }

}
