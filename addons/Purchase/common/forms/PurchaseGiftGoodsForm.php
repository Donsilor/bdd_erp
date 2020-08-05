<?php

namespace addons\Purchase\common\forms;

use Yii;
use addons\Purchase\common\models\PurchaseGiftGoods;
use addons\Style\common\enums\AttrIdEnum;
use common\enums\ConfirmEnum;
use common\helpers\StringHelper;

/**
 * 赠品商品 Form
 *
 */
class PurchaseGiftGoodsForm extends PurchaseGiftGoods
{
    public $ids;
    public $put_in_type;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['put_in_type'], 'integer'],
        ];
        return array_merge(parent::rules() , $rules);
    }
    /**
     * 材质列表
     * @return array
     */
    public function getMaterialTypeMap()
    {
        return Yii::$app->attr->valueMap(AttrIdEnum::MATERIAL_TYPE);
    }
    /**
     * 材质颜色列表
     * @return array
     */
    public function getMaterialColorMap()
    {
        return Yii::$app->attr->valueMap(AttrIdEnum::MATERIAL_COLOR);
    }
    /**
     * 主石类型列表
     * @return array
     */
    public function getMainStoneTypeMap()
    {
        return Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_TYPE);
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

