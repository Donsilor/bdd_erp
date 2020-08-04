<?php

namespace addons\Warehouse\services;

use addons\Warehouse\common\enums\TempletBillStatusEnum;
use addons\Warehouse\common\enums\TempletStatusEnum;
use addons\Warehouse\common\forms\WarehouseTempletBillLForm;
use addons\Warehouse\common\forms\WarehouseTempletBillLGoodsForm;
use addons\Warehouse\common\models\WarehouseTemplet;
use addons\Warehouse\common\models\WarehouseTempletBillGoods;
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
 * 样板入库单
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseTempletBillLService extends Service
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
     * 审核样板收货单(入库单)
     * @param WarehouseTempletBillLForm $form
     * @throws
     */
    public function auditTempletL($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        if($form->audit_status == AuditStatusEnum::PASS){
            $form->bill_status = TempletBillStatusEnum::CONFIRM;
            $billGoods = WarehouseTempletBillGoods::find()->select(['goods_name', 'source_detail_id'])->where(['bill_id' => $form->id])->asArray()->all();
            if(empty($billGoods)){
                throw new \Exception("单据明细不能为空");
            }
            //样板入库
            $templet = WarehouseTempletBillLGoodsForm::findAll(['bill_id'=>$form->id]);
            $ids = $g_ids = [];
            foreach ($templet as $detail){
                $templetM = new WarehouseTemplet();
                $good = [
                    'batch_sn' => (string) rand(10000000000,99999999999),//临时
                    'goods_status' => TempletStatusEnum::IN_STOCK,
                    'style_sn' => $detail->style_sn,
                    'qiban_sn' => $detail->qiban_sn,
                    'goods_name' => $detail->goods_name,
                    'goods_image' => $detail->goods_image,
                    'layout_type' => $detail->layout_type,
                    'finger' => $detail->finger,
                    'finger_hk' => $detail->finger_hk,
                    'suttle_weight' => $detail->suttle_weight,
                    'goods_size' => $detail->goods_size,
                    'stone_weight' => $detail->stone_weight,
                    'stone_size' => $detail->stone_size,
                    'put_in_type' => $form->put_in_type,
                    'supplier_id' => $form->supplier_id,
                    'goods_num' => $detail->goods_num,
                    'goods_weight' => $detail->goods_weight,
                    'cost_price' => $detail->cost_price,
                    'warehouse_id' => $form->to_warehouse_id,
                    'purchase_sn' => $form->delivery_no,
                    'receipt_no' => $form->bill_no,
                    'remark' => $detail->remark,
                    'status' => StatusEnum::ENABLED,
                    'creator_id'=>\Yii::$app->user->identity->getId(),
                    'created_at' => time(),
                ];
                $templetM->attributes = $good;
                if(false === $templetM->save()){
                    throw new \Exception($this->getError($templetM));
                }
                $id = $templetM->attributes['id'];
                $ids[] = $id;
                $g_ids[$id] = $detail->id;
            }
            if($ids){
                foreach ($ids as $id){
                    $templet = WarehouseTemplet::findOne(['id'=>$id]);
                    $batch_sn = \Yii::$app->warehouseService->templet->createBatchSn($templet);
                    //回写收货单货品批次号
                    $g_id = $g_ids[$id]??"";
                    if($g_id){
                        $res = WarehouseTempletBillGoods::updateAll(['batch_sn' => $batch_sn], ['id' => $g_id]);
                        if(false === $res){
                            throw new \Exception("回写入库单货品批次号失败");
                        }
                    }
                }
            }
        }else{
            $form->bill_status = TempletBillStatusEnum::SAVE;
        }
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
    }

}