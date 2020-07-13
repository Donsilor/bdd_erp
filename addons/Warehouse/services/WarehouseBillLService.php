<?php

namespace addons\Warehouse\services;

use addons\Style\common\enums\JintuoTypeEnum;
use addons\Warehouse\common\models\WarehouseBillGoodsL;
use Yii;
use common\components\Service;
use common\helpers\SnHelper;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Purchase\common\enums\ReceiptGoodsStatusEnum;
use addons\Purchase\common\models\PurchaseReceiptGoods;
use addons\Warehouse\common\enums\BillStatusEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\enums\OrderTypeEnum;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;

/**
 * 收货单
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseBillLService extends Service
{

    /**
     * 收货单据汇总
     * @param unknown $bill_id
     */
    public function warehouseBillLSummary($bill_id)
    {
        $result = false;
        $sum = WarehouseBillGoodsL::find()
            ->select(['sum(1) as goods_num', 'sum(cost_price) as total_cost', 'sum(market_price) as total_market'])
            ->where(['bill_id'=>$bill_id])
            ->asArray()->one();
        if($sum) {
            $result = WarehouseBill::updateAll(['goods_num'=>$sum['goods_num']/1, 'total_cost'=>$sum['total_cost']/1, 'total_market'=>$sum['total_market']/1],['id'=>$bill_id]);
        }
        return $result;
    }

    /**
     * 创建收货入库单
     *
     * @param array $bill
     * @param array $goods
     */
    public function createBillL($bill, $goods){
        $billM = new WarehouseBill();
        $billM->attributes = $bill;
        $billM->bill_no = SnHelper::createBillSn($billM->bill_type);
        if(false === $billM->save()){
            throw new \Exception($this->getError($billM));
        }
        $bill_id = $billM->attributes['id'];
        $goodsM = new WarehouseBillGoodsL();
        foreach ($goods as $k => &$good){
            $good['goods_id'] = SnHelper::createGoodsId();
            $good['bill_id'] = $bill_id;
            $good['bill_no'] = $billM->bill_no;
            $good['bill_type'] = $billM->bill_type;
            $goodsM->setAttributes($good);
            if(!$goodsM->validate()){
                throw new \Exception($this->getError($goodsM));
            }
        }
        $value = [];
        $key = array_keys($goods[0]);
        foreach ($goods as $item) {
            $value[] = array_values($item);
            if(count($value)>10){
                $res = Yii::$app->db->createCommand()->batchInsert(WarehouseBillGoodsL::tableName(), $key, $value)->execute();
                if(false === $res){
                    throw new \Exception("创建收货单据明细失败1");
                }
                $value = [];
            }
        }
        if(!empty($value)){
            $res = Yii::$app->db->createCommand()->batchInsert(WarehouseBillGoodsL::tableName(), $key, $value)->execute();
            if(false === $res){
                throw new \Exception("创建收货单据明细失败2");
            }
        }

    }

    /**
     * 收货入库单审核
     * @param $form
     */
    public function auditBillL($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        if($form->audit_status == AuditStatusEnum::PASS){
            $form->bill_status = BillStatusEnum::CONFIRM;
        }else{
            $form->bill_status = BillStatusEnum::SAVE;
        }
        $billGoods = WarehouseBillGoodsL::find()->where(['bill_id' => $form->id])->all();
        if(empty($billGoods)){
            throw new \Exception("单据明细不能为空");
        }
        $bill = WarehouseBill::findOne(['id'=>$form->id]);
        $goods = $bill_goods = $goods_ids = [];
        foreach ($billGoods as $good) {
            $goods_ids[] = $good->goods_id;
            $goods[] = [
                'goods_id' => $good->goods_id,
                'goods_name' =>$good->goods_name,
                'style_sn' => $good->style_sn,
                'product_type_id'=>$good->product_type_id,
                'style_cate_id'=>$good->style_cate_id,
                'style_sex' => $good->style_sex,
                'goods_status'=>GoodsStatusEnum::IN_STOCK,
                'supplier_id'=>$bill->supplier_id,
                'put_in_type'=>$bill->put_in_type,
                'company_id'=> 1,//暂时为1
                'warehouse_id' => $bill->to_warehouse_id?:0,
                'order_detail_id' => $good->order_detail_id??"",
                'order_sn' => $good->order_sn??"",
                //金料信息
                'gold_weight' => $good->gold_weight,
                'suttle_weight' => $good->suttle_weight,
                'gold_loss' => $good->gold_loss,
                'gold_price' => $good->gold_price,
                'gold_amount' => $good->gold_amount,
                'gross_weight' => $good->gross_weight,
                'finger' => $good->finger,
                'product_size' => $good->product_size,
                'produce_sn' => $good->produce_sn,
                'cert_type' => $good->cert_type,
                'cert_id' => $good->cert_id,
                'goods_num' => $good->goods_num,
                //材质信息
                'material' => $good->material,
                'material_type' => $good->material_type,
                'material_color' => $good->material_color,
                'jintuo_type' => $good->jintuo_type,
                //石头信息
                'diamond_carat' => $good->diamond_carat,
                'diamond_clarity' => $good->diamond_clarity,
                'diamond_cut' => $good->diamond_cut,
                'diamond_shape' => $good->diamond_shape,
                'diamond_color' => $good->diamond_color,
                'diamond_polish' => $good->diamond_polish,
                'diamond_symmetry' => $good->diamond_symmetry,
                'diamond_fluorescence' => $good->diamond_fluorescence,
                'diamond_discount' => $good->diamond_discount,
                'diamond_cert_type' => $good->diamond_cert_type,
                'diamond_cert_id' => $good->diamond_cert_id,
                //费用信息
                'market_price' => $good->market_price,
                'cost_price' => $good->cost_price,
                'xiangkou' => $good->xiangkou,
                'bukou_fee' => $good->bukou_fee,
                'biaomiangongyi_fee' => $good->biaomiangongyi_fee,
                'xianqian_fee' => $good->xianqian_fee,
                'gong_fee' => $good->gong_fee,
                'length' => $good->length,
                'parts_gold_weight' => $good->parts_gold_weight,
                'parts_num' => $good->parts_num,
                //主石
                'main_stone_sn' => $good->main_stone_sn,
                'main_stone_type' => $good->main_stone_type,
                'main_stone_num' => $good->main_stone_num,
                'main_stone_price' => $good->main_stone_price,
                //副石1
                'second_cert_id1' => $good->second_cert_id1,
                'second_stone_sn1' => $good->second_stone_sn1,
                'second_stone_type1' => $good->second_stone_type1,
                'second_stone_num1' => $good->second_stone_num1,
                'second_stone_weight1' => $good->second_stone_weight1,
                'second_stone_price1' => $good->second_stone_price1,
                'second_stone_color1' => $good->second_stone_color1,
                'second_stone_clarity1' => $good->second_stone_clarity1,
                'second_stone_shape1' => $good->second_stone_shape1,
                //副石2
                'second_stone_type2' => $good->second_stone_type2,
                'second_stone_num2' => $good->second_stone_num2,
                'second_stone_weight2' => $good->second_stone_weight2,
                'second_stone_price2' => $good->second_stone_price2,
                'remark' => $good->remark,
                'creator_id' => \Yii::$app->user->identity->getId(),
                'created_at' => time(),
            ];
            $bill_goods[] = [
                'bill_id' => $good->bill_id,
                'bill_no' => $bill->bill_no,
                'bill_type' => $bill->bill_type,
                'goods_id' => $good->goods_id,
                'goods_name' => $good->goods_name,
                'style_sn' => $good->style_sn,
                'goods_num' => 1,
                'put_in_type' => $bill->put_in_type,
                'cost_price' => $good->cost_price,
                //'sale_price' => $good->sale_price,
                //'market_price' => $good->market_price,
                'status' => StatusEnum::ENABLED,
                'creator_id' => \Yii::$app->user->identity->getId(),
                'created_at' => time(),
            ];
        }
        $model = new WarehouseGoods();
        $goodsM = new WarehouseBillGoods();
        $value = [];
        $key = array_keys($goods[0]);
        foreach ($goods as $item) {
            $model->setAttributes($item);
            if(!$model->validate()){
                throw new \Exception($this->getError($model));
            }
            $value[] = array_values($item);
        }
        $res = Yii::$app->db->createCommand()->batchInsert(WarehouseGoods::tableName(), $key, $value)->execute();
        if(false === $res){
            throw new \Exception("创建货品信息失败");
        }
        $value = [];
        $key = array_keys($bill_goods[0]);
        foreach ($bill_goods as $item) {
            $goodsM->setAttributes($item);
            if(!$goodsM->validate()){
                throw new \Exception($this->getError($goodsM));
            }
            $value[] = array_values($item);
        }
        $res = Yii::$app->db->createCommand()->batchInsert(WarehouseBillGoods::tableName(), $key, $value)->execute();
        if(false === $res){
            throw new \Exception("创建收货单明细失败");
        }
        //创建货号
        $ids = WarehouseGoods::find()->select(['id'])->where(['goods_id' => $goods_ids])->all();
        $ids = ArrayHelper::getColumn($ids,'id');
        if($ids){
            foreach ($ids as $id) {
                $goods = WarehouseGoods::findOne(['id'=>$id]);
                $old_goods_id = $goods->goods_id;
                $goods_id = \Yii::$app->warehouseService->warehouseGoods->createGoodsId($goods);
                $bGoodsM = WarehouseBillGoods::findOne(['goods_id'=>$old_goods_id]);
                $bGoodsM->goods_id = $goods_id;
                if(false === $bGoodsM->save(true,['id', 'goods_id'])){
                    throw new \Exception($this->getError($bGoodsM));
                }
                $goodsL = WarehouseBillGoodsL::findOne(['goods_id'=>$old_goods_id]);
                $goodsL->goods_id = $goods_id;
                if(false === $goodsL->save(true,['id', 'goods_id'])){
                    throw new \Exception($this->getError($goodsL));
                }
            }
        }
        if($form->order_type == OrderTypeEnum::ORDER_L && $form->audit_status == AuditStatusEnum::PASS){
            //同步采购收货单货品状态
            $ids = ArrayHelper::getColumn($billGoods, 'source_detail_id');
            if($ids){
                $res = PurchaseReceiptGoods::updateAll(['goods_status'=>ReceiptGoodsStatusEnum::WAREHOUSE], ['id'=>$ids]);
                if(false === $res) {
                    throw new \Exception("同步采购收货单货品状态失败");
                }
            }
        }
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
    }

}