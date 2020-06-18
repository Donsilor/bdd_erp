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
        }
        $res = Yii::$app->db->createCommand()->batchInsert(WarehouseBillGoodsL::tableName(), $key, $value)->execute();
        if(false === $res){
            throw new \Exception("创建收货单据明细失败");
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
                'goods_num' => 1,
                'jintuo_type' => JintuoTypeEnum::Chengpin,
                'cost_price' => $good->cost_price,
                //'market_price' => $style->market_price,
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