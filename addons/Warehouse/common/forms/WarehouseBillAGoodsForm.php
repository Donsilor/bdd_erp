<?php

namespace addons\Warehouse\common\forms;

use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Warehouse\common\models\WarehouseBillGoodsA;
use addons\Warehouse\common\models\WarehouseGoods;
use common\enums\AuditStatusEnum;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;


/**
 * 调整单明细 Form
 *
 */
class WarehouseBillAGoodsForm extends WarehouseBillGoodsA
{
    public $goods_ids;
    public $ids;
    public $apply_info;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['goods_ids'], 'required']
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
            'goods_ids' => '货号'
        ]);
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

    /**
     * 初始化 申请表单数据
     */
    public function initApplyView()
    {

        $fields = $this->getMap();
        $goods = WarehouseGoods::find()->where(['goods_id'=>$this->goods_id])->one();
        $attr_field = ['xiangkou','finger','main_stone_type','diamond_color','diamond_shape','diamond_clarity','diamond_cut',
            'diamond_polish','diamond_symmetry','diamond_fluorescence','diamond_cert_type','second_stone_type1',
            'second_stone_color1','second_stone_clarity1','second_stone_shape1','second_stone_type2'];
        $apply_info = [];
        foreach ($fields as $bill_goods_field => $goods_field) {
            $label = $this->getAttributeLabel($bill_goods_field);
            $org_value = $goods->$goods_field;
            $val = $this->$bill_goods_field;
            if(in_array($bill_goods_field, $attr_field)){
                $val = \Yii::$app->attr->valueName($val);
                $org_value = \Yii::$app->attr->valueName($org_value);
            }
            $apply_info[] = ['label'=>$label,'value'=>$val,'org_value'=>$org_value,'changed'=>($val != $org_value)];
        }
        $this->apply_info = $apply_info;
    }

    /**
     * 审核通过后同步数据到库存表和单据明细表
     */
    public function initSynch(){
        //同步数据到库存表
        $fields = $this->getMap();
        $goods = WarehouseGoods::find()->where(['goods_id' => $this->goods_id])->one();
        foreach ($fields as $bill_goods_field => $goods_field){
            $goods -> $goods_field = $this -> $bill_goods_field;
        }
        $goods->goods_status = GoodsStatusEnum::IN_STOCK;
        //$goods->save();
        if(false === $goods->save()) {
            throw new \Exception($this->getErrors($goods));
        }

        //同步数据到单据明细
        $bill_goods = WarehouseBillGoods::find()->where(['bill_id'=>$this->bill_id,'goods_id'=>$this->goods_id])->one();
        $bill_goods->goods_name = $this->goods_name;
        $bill_goods->gold_weight = $this->gold_weight;
        $bill_goods->gold_loss = $this->gold_loss;
        $bill_goods->diamond_carat = $this->diamond_carat;
        $bill_goods->diamond_color = $this->diamond_color;
        $bill_goods->diamond_clarity = $this->diamond_clarity;
        $bill_goods->diamond_cert_id = $this->diamond_cert_id;
        $bill_goods->cost_price = $this->cost_price;
        //$bill_goods->save();
        if(false === $bill_goods->save()) {
            throw new \Exception($this->getErrors($bill_goods));
        }

        //更新单据状态
        $count = WarehouseBillGoodsA::find()->where(['and',['bill_id'=>$this->bill_id],['<>','audit_status',AuditStatusEnum::PASS]])->count();
        $warehouseBill = WarehouseBill::find()->where(['id'=>$this->bill_id])->one();
        if($count == 0){
            $warehouseBill->bill_status = BillStatusEnum::CONFIRM;
            $warehouseBill->audit_status = AuditStatusEnum::PASS;

        }else{
            $warehouseBill->bill_status = BillStatusEnum::PENDING;
            $warehouseBill->audit_status = AuditStatusEnum::PENDING;
        }
        $warehouseBill->save(true,['audit_status','bill_status']);

    }

    //调整单明细字段 => 商品表字段
    public function getMap(){
        return array(
            'goods_name' => 'goods_name',
            'xiangkou' => 'xiangkou',
            'finger' => 'finger',
            'product_size' => 'product_size',
            'gold_weight'  => 'gold_weight',
            'suttle_weight' =>'gross_weight',
            'gold_loss'  =>'gold_loss',
            'gold_price'  => 'gold_price',
            'gold_amount'  => 'gold_amount',
            'main_stone_sn'  => 'main_stone_sn',
            'main_stone_num' =>'main_stone_num',
            'main_stone_type' => 'main_stone_type',
            'main_stone_price' =>'main_stone_price',
            'diamond_shape' =>'diamond_shape',
            'diamond_carat' =>'diamond_carat',
            'diamond_color' =>'diamond_color',
            'diamond_clarity' =>'diamond_clarity',
            'diamond_cut'  =>'diamond_cut',
            'diamond_polish' =>'diamond_polish',
            'diamond_symmetry' =>'diamond_symmetry',
            'diamond_fluorescence' =>'diamond_fluorescence',
            'diamond_cert_type' =>'diamond_cert_type',
            'diamond_cert_id' =>'diamond_cert_id',
            'second_stone_sn1' =>'second_stone_sn1',
            'second_stone_type1' =>'second_stone_type1',
            'second_stone_shape1' =>'second_stone_shape1',
            'second_stone_num1' =>'second_stone_num1',
            'second_stone_weight1' => 'second_stone_weight1',
            'second_stone_color1' => 'second_stone_color1',
            'second_stone_clarity1' =>'second_stone_clarity1',
            'second_stone_price1' =>'second_stone_price1',
            'second_stone_type2' =>'second_stone_type2',
            'second_stone_num2' =>'second_stone_num2',
            'second_stone_weight2' =>'second_stone_weight2',
            'second_stone_price2' =>'second_stone_price2',
            'parts_gold_weight' =>'parts_gold_weight',
            'parts_price' => 'parts_price',
            'gong_fee' => 'gong_fee',
            'bukou_fee'  => 'bukou_fee',
            'xianqian_fee' => 'xianqian_fee',
            'cert_fee' => 'cert_fee',
            'biaomiangongyi_fee' => 'biaomiangongyi_fee',
            'cost_price' => 'cost_price'
        );
    }


}
