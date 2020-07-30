<?php

namespace addons\Warehouse\services;

use Yii;
use common\components\Service;
use common\helpers\SnHelper;
use addons\Warehouse\common\models\WarehouseParts;
use addons\Warehouse\common\models\WarehousePartsBill;
use addons\Warehouse\common\models\WarehousePartsBillGoods;
use addons\Warehouse\common\forms\WarehousePartsBillLGoodsForm;
use addons\Warehouse\common\enums\PartsStatusEnum;
use addons\Purchase\common\models\PurchasePartsReceiptGoods;
use addons\Purchase\common\enums\ReceiptGoodsStatusEnum;
use addons\Warehouse\common\enums\PartsBillStatusEnum;
use common\enums\AuditStatusEnum;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;

/**
 * 配件入库单
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehousePartsBillLService extends Service
{

    /**
     * 创建配件收货单(入库单)
     *
     * @param WarehousePartsBill $bill
     * @param WarehousePartsBillGoods $details
     * @throws
     * @return
     */
    public function createPartsL($bill, $details){
        $billM = new WarehousePartsBill();
        $billM->attributes = $bill;
        $billM->bill_no = SnHelper::createBillSn($billM->bill_type);
        if(false === $billM->save()){
            throw new \Exception($this->getError($billM));
        }
        $bill_id = $billM->attributes['id'];
        $goodsM = new WarehousePartsBillGoods();
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
            if(count($value)>10){
                $res = \Yii::$app->db->createCommand()->batchInsert(WarehousePartsBillGoods::tableName(), $key, $value)->execute();
                if(false === $res){
                    throw new \Exception("创建收货单明细失败1");
                }
                $value=[];
            }
        }
        if(!empty($value)){
            $res = \Yii::$app->db->createCommand()->batchInsert(WarehousePartsBillGoods::tableName(), $key, $value)->execute();
            if(false === $res){
                throw new \Exception("创建收货单明细失败2");
            }
        }
        //单据汇总
        \Yii::$app->warehouseService->partsBill->partsBillSummary($billM->id);
        return $billM;
    }
    /**
     * 审核配件收货单(入库单)
     * @param object $form
     * @throws
     */
    public function auditPartsL($form)
    {
        if(false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        if($form->audit_status == AuditStatusEnum::PASS){
            $form->bill_status = PartsBillStatusEnum::CONFIRM;
            $billGoods = WarehousePartsBillGoods::find()->select(['parts_name', 'source_detail_id'])->where(['bill_id' => $form->id])->asArray()->all();
            if(empty($billGoods)){
                throw new \Exception("单据明细不能为空");
            }
            //配件入库
            $parts = WarehousePartsBillLGoodsForm::findAll(['bill_id'=>$form->id]);
            $ids = $g_ids = [];
            foreach ($parts as $detail){
                $partsM = new WarehouseParts();
                $good = [
                    'parts_sn' => (string) rand(10000000000,99999999999),//临时
                    'parts_status' => PartsStatusEnum::IN_STOCK,
                    'style_sn' => $detail->style_sn,
                    'parts_name' => $detail->parts_name,
                    'parts_type' => $detail->parts_type,
                    'material_type' => $detail->material_type,
                    'shape' => $detail->shape,
                    'color' => $detail->color,
                    'size' => $detail->size,
                    'chain_type' => $detail->chain_type,
                    'cramp_ring' => $detail->cramp_ring,
                    'put_in_type' => $form->put_in_type,
                    'supplier_id' => $form->supplier_id,
                    'parts_num' => $detail->parts_num,
                    'parts_weight' => $detail->parts_weight,
                    'cost_price' => $detail->cost_price,
                    'parts_price' => $detail->parts_price,
                    'warehouse_id' => $form->to_warehouse_id,
                    'remark' => $detail->remark,
                    'status' => StatusEnum::ENABLED,
                    'creator_id'=>\Yii::$app->user->identity->getId(),
                    'created_at' => time(),

                ];
                $partsM->attributes = $good;
                if(false === $partsM->save()){
                    throw new \Exception($this->getError($partsM));
                }
                $id = $partsM->attributes['id'];
                $ids[] = $id;
                $g_ids[$id] = $detail->id;
            }
            if($ids){
                foreach ($ids as $id){
                    $parts = WarehouseParts::findOne(['id'=>$id]);
                    $parts_sn = \Yii::$app->warehouseService->parts->createPartsSn($parts);
                    //回写收货单货品批次号
                    $g_id = $g_ids[$id]??"";
                    if($g_id){
                        $res = WarehousePartsBillGoods::updateAll(['parts_sn' => $parts_sn], ['id' => $g_id]);
                        if(false === $res){
                            throw new \Exception("回写收货单货品批次号失败");
                        }
                    }
                }
            }
            if($form->audit_status == AuditStatusEnum::PASS){
                //同步配件采购收货单货品状态
                $ids = ArrayHelper::getColumn($billGoods, 'source_detail_id');
                $res = PurchasePartsReceiptGoods::updateAll(['goods_status'=>ReceiptGoodsStatusEnum::WAREHOUSE], ['id'=>$ids]);
                if(false === $res) {
                    throw new \Exception("同步配件采购收货单货品状态失败");
                }
            }
        }else{
            $form->bill_status = PartsBillStatusEnum::SAVE;
        }
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
    }

}