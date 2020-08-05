<?php

namespace addons\Purchase\common\forms;

use common\helpers\StringHelper;
use Yii;
use addons\Purchase\common\models\PurchaseGoldGoods;
use common\enums\ConfirmEnum;
use addons\Style\common\enums\AttrIdEnum;

/**
 * 金料商品 Form
 *
 */
class PurchaseGoldGoodsForm extends PurchaseGoldGoods
{
    public $ids;
    public $put_in_type;
    /**
     * 材质列表
     * @return array
     */
    public function getMaterialTypeMap()
    {
        return Yii::$app->attr->valueMap(AttrIdEnum::MAT_GOLD_TYPE);
    }
    /**
     * 采购商品申请编辑-创建
     */
    public function createApply()
    {
        //主要信息
        $fields = array('goods_name','material_type','cost_price','goods_num','gold_price','goods_weight','remark');
        $apply_info = array();
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
     * {@inheritdoc}
     */
    public function getIds(){
        if($this->ids){
            return StringHelper::explode($this->ids);
        }
        return [];
    }
}

