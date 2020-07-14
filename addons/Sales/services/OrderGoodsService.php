<?php

namespace addons\Sales\services;

use addons\Sales\common\enums\IsStockEnum;
use addons\Sales\common\models\OrderGoodsAttribute;
use addons\Style\common\enums\AttrIdEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\models\WarehouseGoods;
use common\components\Service;
use common\enums\StatusEnum;


/**
 * Class SaleChannelService
 * @package services\common
 */
class OrderGoodsService extends Service
{

    /**
     * @param $model
     * @throws \Exception
     * 绑定现货
     */
    public function toStock($model){
//        try{

            if($model->is_stock == IsStockEnum::YES){
                throw new \Exception('请先解绑');
            }
            $wareshouse_goods = WarehouseGoods::find()->where(['goods_id'=>$model->goods_id, 'goods_status'=>GoodsStatusEnum::IN_STOCK])->one();
            if(empty($wareshouse_goods)){
                throw new \Exception("此货号不存在或者不是库存状态",422);
            }
            if($model->qiban_type ===0 ){
                //非起版
                if($model->style_sn != $wareshouse_goods->style_sn){
                    throw new \Exception("订单明细与此货款号不一致",422);
                }
            }else{
                if($model->qiban_sn != $wareshouse_goods->qiban_sn){
                    throw new \Exception("订单明细与此货起版号不一致",422);
                }
            }

            //删除商品属性
            OrderGoodsAttribute::deleteAll(['id'=>$model->id]);

            $attr_list = $this->Attrs();
            foreach ($attr_list as $key => $attr_arr){
                $attr_value_id = (int)$wareshouse_goods->$key;
                if(!$wareshouse_goods->$key){
                    $attr_value_id = 0;
                }
                $attr = [
                    'id' => $model->id,
                    'attr_id' => $attr_arr['attr_id'],
                    'attr_value_id' => $attr_arr['attr_type'] == 2 ? $attr_value_id : 0,
                    'attr_value' => $attr_arr['attr_type'] == 2 ? \Yii::$app->attr->valueName($wareshouse_goods->$key) : $wareshouse_goods->$key,
                ];
                $order_goods_attr = new OrderGoodsAttribute();
                $order_goods_attr->attributes = $attr;
                if(false === $order_goods_attr->save()){
                    throw new \Exception($this->getError($order_goods_attr));
                }
            }

            $wareshouse_goods->goods_status = GoodsStatusEnum::IN_SALE;
            if(false === $wareshouse_goods->save(true,['goods_status'])){
                throw new \Exception($this->getError($wareshouse_goods));
            }

            //订单明细
            $model->is_stock = IsStockEnum::YES; //现货
            if(false === $model->save()){
                throw new \Exception($this->getError($model));
            }

          //更新采购汇总：总金额和总数量
          \Yii::$app->salesService->order->orderSummary($model->order_id);

//        }catch (\Exception $e){
//            echo $e;
//            exit;
//        }

    }


    /**
     * @param $model
     * 解绑
     */
    public function toUntie($model){
        $goods_id = $model->goods_id;
        if(!$goods_id){
            throw new \Exception("参数错误",422);
        }
        $wareshouse_goods = WarehouseGoods::find()->where(['goods_id' => $goods_id,'goods_status'=>GoodsStatusEnum::IN_SALE])->one();
        if(!$wareshouse_goods){
            throw new \Exception("货号不存在或者不是销售中状态，请查询原因",422);
        }
        $wareshouse_goods->goods_status = GoodsStatusEnum::IN_STOCK;
        if(false === $wareshouse_goods->save()){
            throw new \Exception($this->getError($wareshouse_goods));
        }

        //删除商品属性
        OrderGoodsAttribute::deleteAll(['id'=>$model->id]);

        //还原原有商品属性
        $attr_list = json_decode($model->attr_info,true);
        if($attr_list){
            foreach ($attr_list as $attr){
                $order_goods_attr = new OrderGoodsAttribute();
                $order_goods_attr->attributes = $attr;
                if(false === $order_goods_attr->save()){
                    throw new \Exception($this->getError($order_goods_attr));
                }
            }
        }


        //订单明细
        $model->is_stock = IsStockEnum::NO; //现货
        $model->goods_id = '';
        if(false === $model->save()){
            throw new \Exception($this->getError($model));
        }
        //更新采购汇总：总金额和总数量
        \Yii::$app->salesService->order->orderSummary($model->order_id);

    }


    public function Attrs(){
        return [
            'gold_weight'=> [
                'attr_id' => AttrIdEnum::JINZHONG, //金重
                'attr_type' => 1,
            ],
            'finger' => [
                'attr_id' => AttrIdEnum::FINGER, //美号（手寸）
                'attr_type' => 2,
            ],
            'finger_hk' => [
                'attr_id' => AttrIdEnum::PORT_NO, //港号（手寸）
                'attr_type'=> 2
            ],
            'material_type' => [
                'attr_id' => AttrIdEnum::MATERIAL_TYPE, //材质
                'attr_type' => 2
            ],
            'material_color' =>[
                'attr_id' => AttrIdEnum::MATERIAL_COLOR, //材质颜色
                'attr_type' => 2
            ],
            'xiangkou' => [
                'attr_id' => AttrIdEnum::XIANGKOU, //镶口
                'attr_type' => 2
            ],
            'chain_long' => [
                'attr_id' => AttrIdEnum::CHAIN_LENGTH, //链长
                'attr_type' => 1
            ],
            'chain_type' => [
                'attr_id' => AttrIdEnum::CHAIN_TYPE, //链类型
                'attr_type' => 2
            ],
            'cramp_ring' => [
                'attr_id' => AttrIdEnum::CHAIN_BUCKLE, //链扣环
                'attr_type' => 2
            ],
            'biaomiangongyi' =>[
                'attr_id' => AttrIdEnum::FACEWORK, //表面工艺
                'attr_type' => 2
            ],

            'diamond_clarity' => [
                'attr_id' => AttrIdEnum::DIA_CLARITY, //钻石净度
                'attr_type' => 2
            ],
            'diamond_cut' => [
                'attr_id' => AttrIdEnum::DIA_CUT, //钻石切工,
                'attr_type' => 2
            ],
            'diamond_carat' => [
                'attr_id' => AttrIdEnum::DIA_CARAT, //钻石大小
                'attr_type' => 1
            ],
            'diamond_shape' =>[
                'attr_id' => AttrIdEnum::DIA_SHAPE, //钻石形状
                'attr_type' => 2
            ],
            'diamond_color' => [
                'attr_id' => AttrIdEnum::DIA_COLOR, //钻石颜色
                'attr_type' => 2
            ],
            'diamond_fluorescence' => [
                'attr_id' => AttrIdEnum::DIA_FLUORESCENCE, //钻石荧光
                'attr_type' => 2
            ],
            'diamond_cert_id' => [
                'attr_id' => AttrIdEnum::DIA_CERT_NO, //证书编号
                'attr_type' => 1
            ],
            'diamond_cert_type' => [
                'attr_id' => AttrIdEnum::DIA_CERT_TYPE, //主石证书类型
                'attr_type' =>2
            ],
            'main_stone_colour' => [
                'attr_id' => AttrIdEnum::DIA_COLOUR, //钻石色彩
                'attr_type' => 2
            ],
            'diamond_polish' => [
                'attr_id' => AttrIdEnum::DIA_POLISH, //钻石抛光
                'attr_type' => 2
            ],
            'diamond_symmetry' => [
                'attr_id' => AttrIdEnum::DIA_SYMMETRY, //钻石对称
                'attr_type' => 2
            ],
            'main_stone_size' => [
                'attr_id' =>  AttrIdEnum::DIA_SPEC, //钻石规格
                'attr_type' => 1
            ],
            'main_stone_type' => [
                'attr_id' => AttrIdEnum::MAIN_STONE_TYPE, //主石类型
                'attr_type' => 2
            ],
            'main_stone_num' => [
                'attr_id' => AttrIdEnum::MAIN_STONE_NUM, //主石数量
                'attr_type' => 1
            ],

            'second_stone_type1' => [
                'attr_id' => AttrIdEnum::SIDE_STONE1_TYPE, //副石1类型
                'attr_type' => 2
            ],
            'second_stone_shape1' => [
                'attr_id' => AttrIdEnum::SIDE_STONE1_SHAPE, //副石1类型
                'attr_type' => 2
            ],
            'second_stone_color1' => [
                'attr_id' => AttrIdEnum::SIDE_STONE1_COLOR, //副石1颜色
                'attr_type' => 2
            ],
            'second_stone_clarity1' => [
                'attr_id' => AttrIdEnum::SIDE_STONE1_CLARITY, //副石1净度
                'attr_type' => 2
            ],
            'second_stone_weight1' => [
                'attr_id' => AttrIdEnum::SIDE_STONE1_WEIGHT, //副石1重量(ct)
                'attr_type' => 1
            ],
            'second_stone_num1' => [
                'attr_id' => AttrIdEnum::SIDE_STONE1_NUM, //副石1数量
                'attr_type' => 1
            ],

            'second_stone_type2' => [
                'attr_id' => AttrIdEnum::SIDE_STONE2_TYPE, //副石2类型
                'attr_type' => 2
            ],
            'second_stone_shape2' => [
                'attr_id' => AttrIdEnum::SIDE_STONE2_SHAPE, //副石2形状
                'attr_type' => 2
            ],

            'second_stone_weight2' => [
                'attr_id' => AttrIdEnum::SIDE_STONE2_WEIGHT, //副石2重量(ct)
                'attr_type' => 1
            ],
            'second_stone_num2' => [
                'attr_id' => AttrIdEnum::SIDE_STONE2_NUM, //副石2数量
                'attr_type' => 1
            ],

            'product_size' => [
                'attr_id' => AttrIdEnum::PRODUCT_SIZE, //成品尺寸(mm)
                'attr_type' => 1
            ],
            'talon_head_type' => [
                'attr_id' => AttrIdEnum::TALON_HEAD_TYPE, //爪头形状
                'attr_type' => 2
            ],
            'xiangqian_craft' => [
                'attr_id' => AttrIdEnum::XIANGQIAN_CRAFT, //镶嵌工艺
                'attr_type' => 2
            ],
        ];
    }
}