<?php

namespace addons\Purchase\common\forms;

use addons\Purchase\common\models\PurchaseGoods;
use addons\Purchase\common\models\PurchaseGoodsAttribute;
use addons\Purchase\common\models\PurchaseGoodsPrint;
use addons\Style\common\enums\AttrIdEnum;


/**
 * 款式编辑-款式属性 Form
 *
 * @property string $attr_require 必填属性
 * @property string $attr_custom 选填属性
 */
class PurchaseGoodsPrintForm extends PurchaseGoodsPrint
{

    public $supplier_name;
    public $purchase_sn;
    public $goods_name;
    public $created_at;
    public $delivery_time;
    public $material;
    public $goods_num;
    public $main_stone_type;
    public $main_stone_num;
    public $dia_carat;
    public $side_stone1_type;
    public $side_stone1_num;
    public $side_stone1_weight;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {      
         $rules = [
         ];
         return array_merge(parent::rules() , $rules);
    }

    public function getPurchaseInfo(){
        $purchase_goods = PurchaseGoods::find()->where(['id'=>$this->purchase_goods_id])->one();
        $attrs = PurchaseGoodsAttribute::find()->select(['attr_id','attr_value'])->where(['id'=>$this->purchase_goods_id])->asArray()->all();
        $attrs = array_column($attrs,'attr_value','attr_id');

        $supplier_name = $purchase_goods->purchase->supplier->supplier_name ?? '';
        $this->supplier_name = $this->supplier_name ?? $supplier_name;
        $purchase_sn = $purchase_goods->purchase->purchase_sn ?? '';
        $this->purchase_sn = $this->purchase_sn ?? $purchase_sn;
        $this->goods_name = $this->goods_name ?? $purchase_goods->goods_name;
        $created_at = \Yii::$app->formatter->asDate($purchase_goods->purchase->created_at) ?? '';
        $this->created_at = $this->created_at ?? $created_at;
        $delivery_time = \Yii::$app->formatter->asDate($purchase_goods->purchase->delivery_time) ?? '';
        $this->delivery_time = $this->delivery_time ?? $delivery_time;
        $material = $attrs[AttrIdEnum::MATERIAL] ?? '';
        $this->material = $this->material ?? $material;
        $this->goods_num = $this->goods_num ?? $purchase_goods->goods_num;
        $this->factory_model = $this->factory_model ?? $purchase_goods->factory_mo;
        $circle = $attrs[AttrIdEnum::FINGER] ?? '';
        $this->circle = $this->circle ?? $circle;
        $main_stone_type = $attrs[AttrIdEnum::MAIN_STONE_TYPE] ?? '';
        $this->main_stone_type = $this->main_stone_type ?? $main_stone_type;
        $main_stone_num = $attrs[AttrIdEnum::MAIN_STONE_NUM] ?? '';
        $this->main_stone_num = $this->main_stone_num ?? $main_stone_num;
        $dia_carat = $attrs[AttrIdEnum::DIA_CARAT] ?? '';
        $this->dia_carat = $this->dia_carat ?? $dia_carat;
        $side_stone1_type = $attrs[AttrIdEnum::SIDE_STONE1_TYPE] ?? '';
        $this->side_stone1_type = $this->side_stone1_type ?? $side_stone1_type;
        $side_stone1_num = $attrs[AttrIdEnum::SIDE_STONE1_NUM] ?? '';
        $this->side_stone1_num = $this->side_stone1_num ?? $side_stone1_num;
        $side_stone1_weight = $attrs[AttrIdEnum::SIDE_STONE1_WEIGHT] ?? '';
        $this->side_stone1_weight = $this->side_stone1_weight ?? $side_stone1_weight;
        $this->image = $this->image ?? $purchase_goods->goods_image;

    }


}
