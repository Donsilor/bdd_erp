<?php

namespace addons\Gdzb\services;

use addons\Gdzb\common\models\Goods;
use addons\Gdzb\common\models\Order;
use addons\Gdzb\common\models\OrderGoods;
use addons\Sales\common\enums\RefundStatusEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use common\enums\ConfirmEnum;
use common\helpers\StringHelper;
use Yii;
use common\components\Service;



/**
 * Class SaleChannelService
 * @package services\common
 */
class OrderGoodsService extends Service
{

    /**
     * 创建商品编号
     * @param WarehouseGoods $model
     * @param boolean $save
     * @throws \Exception
     * @return string
     */
    public function createGoodsSn($model, $save = true) {
        if(!$model->id) {
            throw new \Exception("编货号失败：id不能为空");
        }
        $prefix   = '';
        //2.商品材质（产品线）
        $type_tag = StringHelper::getFirstCode($model->productType->name) ?? 0;
        $prefix .= $type_tag;
        //3.产品分类
        $cate_tag = StringHelper::getFirstCode($model->styleCate->name) ?? 0;
        $prefix .= $cate_tag;

        //4.数字部分
        $middle = str_pad($model->id,8,'0',STR_PAD_LEFT);
        $model->goods_sn = $prefix.$middle;
        if($save === true) {
            $result = $model->save(true,['goods_sn']);
            if($result === false){
                throw new \Exception("编货号失败：保存货号失败");
            }
        }
        return $model->goods_sn;
    }

    /***
     * 查询库存并更新库存状态
     */
    public function syncGoods($model,$type = 'add'){
        $goods_sn = $model->goods_sn;
        $goods = Goods::find()->where(['goods_sn' => $goods_sn])->one();
        if($goods){
            switch ($type){
                case 'add':
                    if($goods->goods_status != GoodsStatusEnum::IN_STOCK){
                        throw new \Exception("货号不是库存中");
                    }
                    $goods->order_id = $model->order_id;
                    $goods->goods_status = GoodsStatusEnum::IN_SALE;
                    if($goods->save(true,['order_id','goods_status']) === false){
                        throw new \Exception($this->getError($goods));
                    }


                    break;
                case 'del':
                    if($goods->goods_status != GoodsStatusEnum::IN_SALE){
                        throw new \Exception("货号信息有误，请查明原因");
                    }
                    $goods->order_id = '';
                    $goods->goods_status = GoodsStatusEnum::IN_STOCK;
                    if($goods->save(true,['order_id','goods_status']) === false){
                        throw new \Exception($this->getError($goods));
                    }

                    break;

                case 'delivery':
                    if($goods->goods_status != GoodsStatusEnum::IN_SALE){
                        throw new \Exception("货号信息有误，请查明原因");
                    }
                    $goods->goods_status = GoodsStatusEnum::HAS_SOLD;
                    if($goods->save(true,['goods_status']) === false){
                        throw new \Exception($this->getError($goods));
                    }

            }

            return $goods;
        }
        return true;

    }


    //退货
    public function syncRefund($order_id,$ids){
        $order = Order::find()->where(['id'=>$order_id])->one();
        $order_goods = OrderGoods::find()->where(['id'=>$ids])->all();
        $refund_goods = [];
        $refund_price_sum = 0;
        $refund_num = count($order_goods);
        $goods_sns = [];
        foreach ($order_goods as $model){
            if($model['is_return'] == ConfirmEnum::YES){
                throw new \Exception("商品{$model->goods_sn}已经退货");
            }
            $goods_sns[] = $model->goods_sn;
            $refund_goods[] = [
                'goods_sn' =>$model->goods_sn,
                'goods_image' =>$model->goods_image,
                'order_goods_id' =>$model->id,
                'goods_name' =>$model->goods_name,
                'cost_price' =>$model->cost_price,
                'goods_price' =>$model->goods_price,
                'refund_price' =>$model->goods_price,
                'warehouse_id' =>$model->warehouse_id,
                'style_cate_id' =>$model->style_cate_id,
                'product_type_id' =>$model->product_type_id,
            ];

            $refund_price_sum += $model->goods_price;

            //更新订单明细信息
            $model->is_return = ConfirmEnum::YES;
            $model->refund_price = $model->goods_price;
            if($model->save(true,['is_return','refund_price']) === false){
                throw new \Exception($this->getError($model));
            }
        }

        $refund = [
            'order_id' => $order->id,
            'refund_amount' => $refund_price_sum,
            'refund_num' => $refund_num,
            'channel_id' => $order->channel_id,
            'warehouse_id' => $order->warehouse_id,
            'customer_id' => $order->customer_id,
        ];


        //重新统计
        Yii::$app->gdzbService->order->orderSummary($model->order_id);


        $return = \Yii::$app->gdzbService->orderRefund->createSyncRefund($refund,$refund_goods);

        $return['order_sn'] = $order->order_sn;
        $return['order_status'] = $order->order_status;

    }



}