<?php

namespace addons\Warehouse\services;

use Yii;
use common\components\Service;
use common\helpers\SnHelper;
use addons\Warehouse\common\models\WarehouseGold;
use addons\Purchase\common\models\PurchaseGoldReceiptGoods;
use addons\Warehouse\common\models\WarehouseGoldBill;
use addons\Warehouse\common\models\WarehouseGoldBillGoods;
use addons\Warehouse\common\forms\WarehouseGoldBillLGoodsForm;
use addons\Warehouse\common\enums\GoldStatusEnum;
use addons\Purchase\common\enums\ReceiptGoodsStatusEnum;
use addons\Warehouse\common\enums\BillStatusEnum;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;

/**
 * 金料入库单
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseGoldBillLService extends Service
{
    /**
     * 创建金料收货单(入库单)
     * @param array $bill
     * @param array $details
     * @throws
     */
    public function createGoldL($bill, $details){
        $billM = new WarehouseGoldBill();
        $billM->attributes = $bill;
        $billM->bill_no = SnHelper::createBillSn($billM->bill_type);
        if(false === $billM->save()){
            throw new \Exception($this->getError($billM));
        }
        $bill_id = $billM->attributes['id'];
        $goodsM = new WarehouseGoldBillGoods();
        foreach ($details as &$good){
            $good['bill_id'] = $bill_id;
            $good['bill_no'] = $billM->bill_no;
            $good['bill_type'] = $billM->bill_type;
            $goodsM->setAttributes($good);
            if(!$goodsM->validate()){
                throw new \Exception($this->getError($goodsM));
            }
        }
        $details = ArrayHelper::toArray($details);
        $value = [];
        $key = array_keys($details[0]);
        foreach ($details as $detail) {
            $value[] = array_values($detail);
        }
        $res = \Yii::$app->db->createCommand()->batchInsert(WarehouseGoldBillGoods::tableName(), $key, $value)->execute();
        if(false === $res){
            throw new \Exception("创建收货单明细失败");
        }
        //单据汇总
        \Yii::$app->warehouseService->goldBill->goldBillSummary($billM->id);
        return $billM;
    }
    /**
     * 审核金料收货单(入库单)
     * @param object $form
     * @throws
     */
    public function auditGoldL($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        if($form->audit_status == AuditStatusEnum::PASS){
            $form->bill_status = BillStatusEnum::CONFIRM;
            $billGoods = WarehouseGoldBillGoods::find()->select(['gold_name', 'source_detail_id'])->where(['bill_id' => $form->id])->asArray()->all();
            if(empty($billGoods)){
                throw new \Exception("单据明细不能为空");
            }
            //金料入库
            $gold = WarehouseGoldBillLGoodsForm::findAll(['bill_id'=>$form->id]);
            $ids = $g_ids = [];
            foreach ($gold as $detail){
                $goldM = new WarehouseGold();
                $good = [
                    'gold_sn' => (string) rand(10000000000,99999999999),//临时
                    'gold_status' => GoldStatusEnum::IN_STOCK,
                    'style_sn' => $detail->style_sn,
                    'gold_name' => $detail->gold_name,
                    'gold_type' => $detail->gold_type,
                    'put_in_type' => $form->put_in_type,
                    'supplier_id' => $form->supplier_id,
                    'gold_num' => $detail->gold_num,
                    'gold_weight' => $detail->gold_weight,
                    'first_weight' => $detail->gold_weight,
                    'cost_price' => $detail->cost_price,
                    'gold_price' => $detail->gold_price,
                    'warehouse_id' => $form->to_warehouse_id,
                    'remark' => $detail->remark,
                    'status' => StatusEnum::ENABLED,
                    'creator_id'=>\Yii::$app->user->identity->getId(),
                    'created_at' => time(),

                ];
                $goldM->attributes = $good;
                if(false === $goldM->save()){
                    throw new \Exception($this->getError($goldM));
                }
                $id = $goldM->attributes['id'];
                $ids[] = $id;
                $g_ids[$id] = $detail->id;
            }
            if($ids){
                foreach ($ids as $id){
                    $stone = WarehouseGold::findOne(['id'=>$id]);
                    $gold_sn = \Yii::$app->warehouseService->gold->createGoldSn($stone);
                    //回写收货单货品批次号
                    $g_id = $g_ids[$id]??"";
                    if($g_id){
                        $res = WarehouseGoldBillGoods::updateAll(['gold_sn' => $gold_sn], ['id' => $g_id]);
                        if(false === $res){
                            throw new \Exception("回写收货单货品批次号失败");
                        }
                    }
                }
            }
            if($form->audit_status == AuditStatusEnum::PASS){
                //同步金料采购收货单货品状态
                $ids = ArrayHelper::getColumn($billGoods, 'source_detail_id');
                $res = PurchaseGoldReceiptGoods::updateAll(['goods_status'=>ReceiptGoodsStatusEnum::WAREHOUSE], ['id'=>$ids]);
                if(false === $res) {
                    throw new \Exception("同步金料采购收货单货品状态失败");
                }
            }
        }else{
            $form->bill_status = BillStatusEnum::SAVE;
        }
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
    }

}