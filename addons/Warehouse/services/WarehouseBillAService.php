<?php

namespace addons\Warehouse\services;


use addons\Warehouse\common\forms\WarehouseBillAForm;
use addons\Warehouse\common\forms\WarehouseBillAGoodsForm;
use addons\Warehouse\common\models\WarehouseBillGoodsA;
use common\components\Service;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use common\enums\AuditStatusEnum;
use common\helpers\StringHelper;


/**
 * 收货单
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseBillAService extends Service
{

    /***
     * 添加信息到单据明细
     */
    public function addBillGoods($model){
            $goods_ids =  StringHelper::explodeIds($model->goods_ids);
            $bill = WarehouseBillAForm::find()->where(['id'=>$model->bill_id])->select(['bill_no','bill_type','from_warehouse_id'])->one();
            foreach ($goods_ids as $goods_id){
                $warehouseBillGoods = new WarehouseBillGoods();
                $goods = WarehouseGoods::find()->where(['goods_id'=>$goods_id,'goods_status'=>GoodsStatusEnum::IN_STOCK])->one();
                if(empty($goods)){
                    throw new \Exception("货号{$goods_id}不存在或者不在库存中");
                }
                if($goods->warehouse_id != $bill->from_warehouse_id){
                    throw new \Exception("货号{$goods_id}仓库与单据仓库不一样");
                }

                //更新库存状态为调整中
                $goods->goods_status = GoodsStatusEnum::IN_ADJUS;
                $goods->save(false);

                //同步库存数据到单据明细
                $warehouse_bill_goods = array(
                    'goods_id' => $goods->goods_id,
                    'goods_name' => $goods->goods_name,
                    'style_sn' => $goods->style_sn,
                    'goods_num' => $goods->goods_num,
                    'order_detail_id' => $goods->order_detail_id,
                    'put_in_type' => $goods->put_in_type,
                    'warehouse_id' => $goods->warehouse_id,
                    'material' => $goods->material,
                    'gold_weight' => $goods->gold_weight,
                    'gold_loss' => $goods->gold_loss,
                    'diamond_carat' => $goods->diamond_carat,
                    'diamond_color' => $goods->diamond_color,
                    'diamond_clarity' => $goods->diamond_clarity,
                    'diamond_cert_id' => $goods->diamond_cert_id,
                    'cost_price' => $goods->cost_price,
                    'market_price' => $goods->market_price,
                    'from_warehouse_id' => $goods->warehouse_id,
                    'markup_rate' => $goods->markup_rate,
                    'goods_remark' => $goods->remark,

                );
                $warehouseBillGoods->attributes = $warehouse_bill_goods;
                $warehouseBillGoods->bill_id = $model->bill_id;
                $warehouseBillGoods->bill_no = $bill->bill_no;
                $warehouseBillGoods->bill_type = $bill->bill_type;
                $warehouseBillGoods->created_at = time();
                $warehouseBillGoods->creator_id = \Yii::$app->user->id;
                if(false === $warehouseBillGoods->save(true)) {
                    throw new \Exception($this->getError($warehouseBillGoods));
                }

                $warehouseBillGoodsA = new WarehouseBillGoodsA();
                $fields_map = WarehouseBillAGoodsForm::getMap();
                $warehouse_bill_goods_a = [];
                foreach ($fields_map as $bill_goods_field => $goods_field){
                    $warehouse_bill_goods_a[$bill_goods_field] = $goods->$goods_field;
                }
                $warehouse_bill_goods_a['goods_id'] = $goods_id;
                $warehouse_bill_goods_a['style_sn'] = $goods->style_sn;


                $warehouseBillGoodsA->attributes = $warehouse_bill_goods_a;
                $warehouseBillGoodsA->bill_id = $model->bill_id;
                if(false === $warehouseBillGoodsA->save(true)) {
                    throw new \Exception($this->getError($warehouseBillGoodsA));
                }

            }

        //汇总
        \Yii::$app->warehouseService->bill->warehouseBillSummary($model->bill_id);
    }


    //获取已经调整的明细
    public function getDjustNum($bill_id){
        return WarehouseBillGoodsA::find()->where(['bill_id'=>$bill_id,'audit_status'=>AuditStatusEnum::PASS])->count();
    }

}