<?php

namespace addons\Warehouse\services;

use addons\Warehouse\common\enums\GoldStatusEnum;
use addons\Warehouse\common\forms\WarehouseGoldBillTGoodsForm;
use addons\Warehouse\common\models\WarehouseGold;
use common\enums\StatusEnum;
use common\helpers\Url;
use Yii;
use common\components\Service;
use common\helpers\SnHelper;
use addons\Warehouse\common\models\WarehouseGoldBill;
use addons\Warehouse\common\models\WarehouseGoldBillGoods;
use common\helpers\ArrayHelper;

/**
 * 领料单
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseGoldBillTService extends Service
{

    /**
     * 审核金料收货单(入库单)
     * @param object $form
     * @throws
     */
    public function createGold($form)
    {
            //金料入库
            $gold = WarehouseGoldBillTGoodsForm::findAll(['bill_id'=>$form->id]);
            $ids = $g_ids = [];
            foreach ($gold as $detail){
                $goldM = new WarehouseGold();
                $gold_sn = $detail->gold_sn;
                $good = [
                    'gold_sn' => empty($gold_sn) ?(string) rand(10000000000,99999999999) : $gold_sn,
                    'gold_status' => GoldStatusEnum::IN_STOCK,
                    'style_sn' => $detail->style_sn,
                    'gold_name' => $detail->gold_name,
                    'gold_type' => $detail->gold_type,
                    'supplier_id' => $form->supplier_id,
                    'gold_num' => $detail->gold_num,
                    'gold_weight' => $detail->gold_weight,
                    'first_weight' => $detail->gold_weight,
                    'cost_price' => $detail->gold_price * $detail->gold_weight,
                    'first_cost_price' => $detail->gold_price * $detail->gold_weight,
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
                if(empty($gold_sn)){
                    $ids[] = $id;
                }
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
        if(false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
    }

}