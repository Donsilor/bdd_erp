<?php

namespace addons\Warehouse\services;

use Yii;
use addons\Warehouse\common\enums\TempletStatusEnum;
use addons\Warehouse\common\models\WarehouseTemplet;
use addons\Warehouse\common\models\WarehouseTempletBill;
use addons\Warehouse\common\models\WarehouseTempletBillGoods;
use common\enums\StatusEnum;
use common\components\Service;
use common\helpers\SnHelper;
use common\helpers\ArrayHelper;

/**
 * 出库单
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseTempletBillCService extends Service
{
    /**
     * 创建出库单
     * @param array $bill
     * @param array $details
     * @throws
     */
    public function createTempletC($bill, $details){
        $billM = new WarehouseTempletBill();
        $billM->attributes = $bill;
        $billM->bill_no = SnHelper::createBillSn($billM->bill_type);
        if(false === $billM->save()){
            throw new \Exception($this->getError($billM));
        }
        $goodsM = new WarehouseTempletBillGoods();
        foreach ($details as &$good){
            $good['bill_id'] = $billM->id;
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
            if(count($value)>=10){
                $res = \Yii::$app->db->createCommand()->batchInsert(WarehouseTempletBillGoods::tableName(), $key, $value)->execute();
                if(false === $res){
                    throw new \Exception("创建出库单明细失败1");
                }
                $value = [];
            }
        }
        if(!empty($value)){
            $res = \Yii::$app->db->createCommand()->batchInsert(WarehouseTempletBillGoods::tableName(), $key, $value)->execute();
            if(false === $res){
                throw new \Exception("创建出库单明细失败2");
            }
        }
        //单据汇总
        \Yii::$app->warehouseService->templetBill->BillSummary($billM->id);
        return $billM;
    }

    /**
     * 添加单据明细
     * @param WarehouseTempletBillGoods $form
     * @throws
     */
    public function createBillGoods($form)
    {
        $templet = WarehouseTemplet::findOne(['batch_sn'=>$form->batch_sn]);
        if(!$templet){
            throw new \Exception("批次号不存在");
        }
        if($templet->goods_status != TempletStatusEnum::IN_STOCK){
            throw new \Exception("批次号不是库存状态");
        }
        $bill = WarehouseTempletBill::findOne($form->bill_id);
        $goods = [
            'bill_id' => $bill->id,
            'bill_no' => $bill->bill_no,
            'bill_type' => $bill->bill_type,
            'layout_type' => $templet->layout_type,
            'goods_name' => $templet->goods_name,
            'goods_image' => $templet->goods_image,
            'stone_weight' => $templet->stone_weight,
            'style_sn' => $templet->style_sn,
            'qiban_sn' => $templet->qiban_sn,
            'finger' => $templet->finger,
            'finger_hk' => $templet->finger_hk,
            'goods_size' => $templet->goods_size,
            'suttle_weight' => $templet->suttle_weight,
            'stone_size' => $templet->stone_size,
            'status' => StatusEnum::ENABLED,
            'created_at' => time()
        ];
        $form->attributes = $goods;
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
        $templet->goods_status = TempletStatusEnum::SOLD_OUT;
        if(false === $templet->save()) {
            throw new \Exception($this->getError($templet));
        }
    }
}