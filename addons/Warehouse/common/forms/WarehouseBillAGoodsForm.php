<?php

namespace addons\Warehouse\common\forms;

use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\enums\PeiJianWayEnum;
use addons\Warehouse\common\enums\PeiLiaoWayEnum;
use addons\Warehouse\common\enums\PeiShiWayEnum;
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

        //金料成本=金价*金重*（1+损耗）
        $goods->gold_amount = $this->gold_price * $this->gold_weight * (1 + $this->gold_loss);
        //主石成本 = 主石重 * 主石买入单价
        $goods->main_stone_cost = $this->main_stone_price * $this->diamond_carat;
        //副石1成本 = 副石1重 * 副石1买入单价
        $goods->second_stone_cost1 = $this->second_stone_price1 * $this->second_stone_weight1;
        //副石2成本 = 副石2重 * 副石2买入单价
        $goods->second_stone_cost2 = $this->second_stone_price2 * $this->second_stone_weight2;
        //副石3成本 = 副石2重 * 副石2买入单价
        $goods->second_stone_cost3 = $this->second_stone_price3 * $this->second_stone_weight3;
        //【配件额=配件重*配件金价】
        $goods->parts_amount = $this->parts_price * $this->parts_gold_weight;
        //配石费 = 配石工费 * 配石重量
        $goods->peishi_amount = $this->peishi_fee * $this->peishi_weight;
        //【镶石1费=镶石1单价/颗*副石1费用；镶石2费=镶石2单价/颗*副石2费用；镶石3费=镶石3单价/颗*副石3费用】
        // 镶石费=镶石1费+镶石2费+镶石3费
        $goods->xianqian_fee = $this->second_stone_fee1 * $this->second_stone_num1 + $this->second_stone_fee2 * $this->second_stone_num2
            + $this->second_stone_fee3 * $this->second_stone_num3;

        //总工费【自动计算】=所有工费【基本工费+配件工费+配石工费+镶石费+表面工艺费+分色费+喷砂费+补口工费+版费 + 证书费 + 其它费用】
        $goods->total_gong_fee = $this->gong_fee + $this->parts_fee + $goods->peishi_amount + $goods->xianqian_fee + $this->biaomiangongyi_fee
            + $this->fense_fee + $this->penrasa_fee + $this->bukou_fee + $this->edition_fee + $this->cert_fee + $this->other_fee
            + $this->lasha_fee + $this->piece_fee;

        //公司成本 = 金料成本 + 主石成本 + 副石1成本 + 副石2成本 + 配件额 + 总工费
        $goods->cost_price = $goods->gold_amount + $goods->main_stone_cost + $goods->second_stone_cost1 + $goods->second_stone_cost2 +
                            $goods->parts_amount + $goods->total_gong_fee;

        //工厂成本
        $goods->factory_cost = 0;
        if($goods->main_peishi_way == PeiShiWayEnum::FACTORY){
            $goods->factory_cost += $goods->main_stone_cost;
        }
        if($goods->second_peishi_way1 == PeiShiWayEnum::FACTORY){
            $goods->factory_cost += $goods->second_stone_cost1;
        }
        if($goods->second_peishi_way2 == PeiShiWayEnum::FACTORY){
            $goods->factory_cost += $goods->second_stone_cost2;
        }
        if($goods->second_peishi_way3 == PeiShiWayEnum::FACTORY){
            $goods->factory_cost += $goods->second_stone_cost3;
        }
        if($goods->peiliao_way == PeiLiaoWayEnum::FACTORY){
            $this->factory_cost += $goods->gold_amount;
        }
        if($goods->peijian_way == PeiJianWayEnum::FACTORY){
            $goods->factory_cost += $goods->parts_amount;
        }
        $goods->factory_cost += $goods->total_gong_fee;

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
            'second_stone_sn2' =>'second_stone_sn2',
            'second_stone_type2' =>'second_stone_type2',
            'second_stone_num2' =>'second_stone_num2',
            'second_stone_weight2' =>'second_stone_weight2',
            'second_stone_price2' =>'second_stone_price2',
            'parts_gold_weight' =>'parts_gold_weight',
            'parts_price' => 'parts_price',
            'gong_fee' => 'gong_fee',
            'parts_fee' => 'parts_fee',
            'fense_fee' => 'fense_fee',
            'penrasa_fee' => 'penrasa_fee',
            'edition_fee' => 'edition_fee',
            'other_fee' => 'other_fee',
            'xianqian_price' => 'xianqian_price',
            'peishi_weight' => 'peishi_weight',
            'bukou_fee'  => 'bukou_fee',
            'second_stone_fee1' => 'second_stone_fee1',
            'second_stone_fee2' => 'second_stone_fee2',
            'second_stone_fee3' => 'second_stone_fee3',
            'cert_fee' => 'cert_fee',
            'biaomiangongyi_fee' => 'biaomiangongyi_fee',
            'cost_price' => 'cost_price',
            'second_stone_sn3' => 'second_stone_sn3',
            'second_stone_type3' => 'second_stone_type3',
            'second_stone_num3' => 'second_stone_num3',
            'second_stone_weight3' => 'second_stone_weight3',
            'second_stone_price3' => 'second_stone_price3',
            'piece_fee' => 'piece_fee',
            'pure_gold' => 'pure_gold',
            'lasha_fee' => 'lasha_fee',
        );
    }


}
