<?php

namespace addons\Sales\services;

use addons\Sales\common\enums\IsGiftEnum;
use addons\Sales\common\enums\IsStockEnum;
use addons\Sales\common\models\Order;
use addons\Sales\common\models\OrderGoodsAttribute;
use addons\Style\common\enums\AttrIdEnum;
use addons\Style\common\enums\JintuoTypeEnum;
use addons\Style\common\enums\QibanTypeEnum;
use addons\Style\common\enums\StoneEnum;
use addons\Style\common\models\Diamond;
use addons\Style\common\models\Qiban;
use addons\Style\common\models\Style;
use addons\Supply\common\enums\BuChanEnum;
use addons\Warehouse\common\enums\GiftBillStatusEnum;
use addons\Warehouse\common\enums\GiftBillTypeEnum;
use addons\Warehouse\common\enums\GoodsStatusEnum;
use addons\Warehouse\common\models\WarehouseGift;
use addons\Warehouse\common\models\WarehouseGoods;
use common\components\Service;
use common\enums\AuditStatusEnum;
use common\enums\InputTypeEnum;
use common\enums\StatusEnum;
use common\helpers\ArrayHelper;


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
            //销售后更新出库成本
            $chuku_price = \Yii::$app->warehouseService->warehouseGoods->getChukuPrice($wareshouse_goods->goods_id);
            $wareshouse_goods->chuku_price = $chuku_price;
            if(false === $wareshouse_goods->save(true,['goods_status','chuku_price'])){
                throw new \Exception($this->getError($wareshouse_goods));
            }

            //订单明细
            $model->bc_status = BuChanEnum::NO_PRODUCTION;
            $model->is_stock = IsStockEnum::YES; //现货
            if(false === $model->save()){
                throw new \Exception($this->getError($model));
            }



    }


    /**
     * @param $model
     * @throws \Exception
     * 添加现货
     */
    public function addStock($model){
        $wareshouse_goods = WarehouseGoods::find()->where(['goods_id'=>$model->goods_id, 'goods_status'=>GoodsStatusEnum::IN_STOCK])->one();
        if(empty($wareshouse_goods)){
            throw new \Exception("不存在或者不是库存状态",422);
        }
        $model->jintuo_type = $wareshouse_goods->jintuo_type;
        $model->qiban_type = $wareshouse_goods->qiban_type;
        $model->style_sex = $wareshouse_goods->style_sex;
        $model->style_cate_id = $wareshouse_goods->style_cate_id;
        $model->product_type_id = $wareshouse_goods->product_type_id;
        $model->goods_num = 1;
        $model->goods_price = 0;
        $model->goods_pay_price = 0;
        $model->goods_name = $wareshouse_goods->goods_name;
        $model->style_sn = $wareshouse_goods->style_sn;
        $model->qiban_sn = $wareshouse_goods->qiban_sn;
        $model->currency = $model->order->currency;
        $isNewRecord = $model->isNewRecord;

        if(false === $model->save(false)){
            throw new \Exception($this->getError($model));
        }

        if($isNewRecord) {
            \Yii::$app->salesService->orderGoods->toStock($model);
        }

        return $model;

    }


    /**
     * @param $model
     * @throws \Exception
     * 添加裸钻
     */
    public function addDiamond($model){
        $diamond_goods = Diamond::find()->where(['cert_id'=>$model->cert_id, 'audit_status'=>AuditStatusEnum::PASS])->one();
        if(empty($diamond_goods)){
            throw new \Exception("此裸钻不存在或者不是审核状态",422);
        }
        //删除商品属性
        OrderGoodsAttribute::deleteAll(['id'=>$model->id]);
        $attr_list = \Yii::$app->styleService->diamond->getMapping();
        foreach ($attr_list as  $attr){
            $attr_value_id = $attr['input_type'] == InputTypeEnum::INPUT_TEXT ? 0 : $diamond_goods->{$attr['attr_field']} ?? 0;
            $attr_value = $attr['input_type'] == InputTypeEnum::INPUT_TEXT ? $diamond_goods->{$attr['attr_field']} : \Yii::$app->attr->valueName($attr_value_id) ?? '';
            $order_goods_attr = new OrderGoodsAttribute();
            $order_goods_attr->id = $model->id;
            $order_goods_attr->attr_id = $attr['attr_id'];
            $order_goods_attr->attr_value_id = $attr_value_id;
            $order_goods_attr->attr_value = $attr_value;
            if(false === $order_goods_attr->save()){
                throw new \Exception($this->getError($order_goods_attr));
            }
        }



        //现货裸钻同步数据到库存
        if($diamond_goods->is_stock == IsStockEnum::YES){
//            print_r($diamond_goods->toArray());
            $cert_id = $diamond_goods['cert_id'];
            $warehouse_goods = WarehouseGoods::find()->where(['cert_id'=>$cert_id])->one();
            if($warehouse_goods){
                $warehouse_goods->goods_status = GoodsStatusEnum::IN_SALE;
                if(false === $warehouse_goods->save(true,['goods_status'])){
                    throw new \Exception($this->getError($warehouse_goods));
                }
            }else{
                $goods = [
                    'goods_id' => $diamond_goods['goods_sn'],
                    'style_sn' => $model->style_sn,
                    'goods_name' => $diamond_goods['goods_name'],
                    'goods_image' => $diamond_goods['goods_image'],
                    'cert_type' => $diamond_goods['cert_type'],
                    'cert_id' => $diamond_goods['cert_id'],
                    'goods_num' => $diamond_goods['goods_num'],
                    'cost_price' => $diamond_goods['cost_price'],
                    'market_price' => $diamond_goods['market_price'],
                    'diamond_carat' => $diamond_goods['carat'],
                    'diamond_clarity' => (string)$diamond_goods['clarity'],
                    'diamond_cut' => (string)$diamond_goods['cut'],
                    'diamond_shape' => (string)$diamond_goods['shape'],
                    'diamond_symmetry' => (string)$diamond_goods['symmetry'],
                    'diamond_polish' => (string)$diamond_goods['polish'],
                    'diamond_fluorescence' => (string)$diamond_goods['fluorescence'],
                    'diamond_discount' => (string)$diamond_goods['source_discount'],
                    'remark' => $diamond_goods['remark'],
                    'goods_status' => GoodsStatusEnum::IN_SALE,
                    'sales_time' => time(),
                    'product_type_id' => 1,
                    'style_cate_id' => 15,
                    'warehouse_id' => 17, //裸钻库
                    'jintuo_type' => JintuoTypeEnum::Chengpin,
                ];

                \Yii::$app->warehouseService->warehouseGoods->createWarehouseGoods($goods);
            }


        }



        //修改裸钻状态
        $diamond_goods->status = StatusEnum::DISABLED;
        if(false === $diamond_goods->save(true,['status'])){
            throw new \Exception($this->getError($diamond_goods));
        }

        //更新采购汇总：总金额和总数量
        \Yii::$app->salesService->order->orderSummary($model->order_id);

    }


    /**
     * @param $model
     * @throws \Exception
     * 删除裸钻
     */
    public function delDiamond($model){
        $cert_attr = OrderGoodsAttribute::find()->where(['id'=>$model->id, 'attr_id'=>AttrIdEnum::DIA_CERT_NO])->one();
        $cert_id = $cert_attr->attr_value ?? 0;
        $diamond_goods = Diamond::find()->where(['cert_id'=>$cert_id, 'audit_status'=>AuditStatusEnum::PASS])->one();
        if(empty($diamond_goods)){
            throw new \Exception("此裸钻不存在或者不是审核状态".$model->cert_id,422);
        }
        //修改裸钻状态
        $diamond_goods->status = StatusEnum::ENABLED;
        if(false === $diamond_goods->save(true,['status'])){
            throw new \Exception($this->getError($diamond_goods));
        }

    }




    /**
     * @param $model
     * @throws \Exception
     * 添加赠品
     */
    public function addGift($model,$num){

        $gift_goods = WarehouseGift::find()->where(['gift_sn'=>$model->goods_sn])->andWhere(['>=','gift_num',($num * -1)])->one();
        if(empty($gift_goods)){
            throw new \Exception("此赠品不存在或者库存不足",422);
        }
        $model->jintuo_type = JintuoTypeEnum::Chengpin;
        $model->qiban_type = QibanTypeEnum::NON_VERSION;
        $model->style_sex = $gift_goods->style_sex;
        $model->style_cate_id = $gift_goods->style_cate_id;
        $model->product_type_id = $gift_goods->product_type_id;
        $model->goods_name = $gift_goods->gift_name;
        $model->is_stock = IsStockEnum::YES;
        $model->is_gift = IsGiftEnum::YES;
        $model->goods_price = $model->goods_price ?? 0;
        $model->goods_pay_price = $model->goods_price ?? 0;
        $model->goods_discount = $model->goods_price - $model->goods_pay_price;
        $model->style_sn = $gift_goods->style_sn;
        $model->qiban_sn = '';
        $model->goods_sn = $gift_goods->gift_sn;
        $model->goods_image = \Yii::$app->warehouseService->gift->getStyleImage($gift_goods);
        $model->currency = $model->order->currency;
        $model->goods_id = '';

        if(false === $model->save()){
            throw new \Exception($this->getError($model));
        }

        if($num == 0){
            return $model;
        }

        $order = Order::find()->where(['id'=>$model->order_id])->one();
        //删除商品属性
        OrderGoodsAttribute::deleteAll(['id'=>$model->id]);
        $attr_list = \Yii::$app->warehouseService->gift->getMapping();
        print_r($attr_list);exit;
        foreach ($attr_list as  $attr){
            $attr_value_id = $attr['input_type'] == InputTypeEnum::INPUT_TEXT ? 0 : $gift_goods->{$attr['attr_field']} ?? 0;
            if($attr['input_type'] == InputTypeEnum::INPUT_SELECT && !$attr_value_id){
                continue;
            }
            $attr_value = $attr['input_type'] == InputTypeEnum::INPUT_TEXT ? $gift_goods->{$attr['attr_field']} : \Yii::$app->attr->valueName($attr_value_id) ?? '';

            $order_goods_attr = new OrderGoodsAttribute();
            $order_goods_attr->id = $model->id;
            $order_goods_attr->attr_id = $attr['attr_id'];
            $order_goods_attr->attr_value_id = $attr_value_id;
            $order_goods_attr->attr_value = $attr_value;
            if(false === $order_goods_attr->save()){
                throw new \Exception($this->getError($order_goods_attr));
            }
        }

        $stock_num = bcadd($gift_goods->gift_num, $num);
        if($stock_num < 0){
            throw new \Exception("赠品库存不够",422);
        }
        $bill_status = $num > 0 ? GiftBillStatusEnum::GIFT_CANCE_LOCK : GiftBillStatusEnum::GIFT_LOCK;
        $gift_bill_info = [
            'gift_id' => $gift_goods->id,
            'bill_no' => $order->order_sn,
            'bill_type' => GiftBillTypeEnum::GIFT_ORDER,
            'num' => $num ,
            'stock_num' => $stock_num,
            'channel_id' => $order->sale_channel_id,
            'bill_status' => $bill_status,
            'remark' => '订单明细添加/编辑赠品'


        ];
        //修改赠品信息
        \Yii::$app->warehouseService->gift->createBill($gift_bill_info);
        //更新采购汇总：总金额和总数量
        \Yii::$app->salesService->order->orderSummary($model->order_id);
        return $model;
    }


    /**
     * @param $model
     * @throws \Exception
     * 删除赠品
     */
    public function delGift($model){
        $order = Order::find()->where(['id'=>$model->order_id])->one();
        $gift_goods = WarehouseGift::find()->where(['gift_sn'=>$model->goods_sn])->one();
        if(empty($gift_goods)){
            throw new \Exception("此赠品不存在或者没有库存",422);
        }
        $num = $model->goods_num;
        $stock_num = bcadd($gift_goods->gift_num, $num);
        $bill_status =  GiftBillStatusEnum::GIFT_CANCE_LOCK ;
        $gift_bill_info = [
            'gift_id' => $gift_goods->id,
            'bill_no' => $order->order_sn,
            'bill_type' => GiftBillTypeEnum::GIFT_ORDER,
            'num' => $num ,
            'stock_num' => $stock_num,
            'channel_id' => $order->sale_channel_id,
            'bill_status' => $bill_status,
            'remark' => '订单明细删除赠品'


        ];
        //修改赠品信息
        $gift_bill = \Yii::$app->warehouseService->gift->createBill($gift_bill_info);
        return $gift_bill;
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

        //还原原有商品属性
        $attr_list = json_decode($model->attr_info,true);
        if($attr_list){
            //删除商品属性
            OrderGoodsAttribute::deleteAll(['id'=>$model->id]);
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
            'material_type' => [
                'attr_id' => AttrIdEnum::MATERIAL_TYPE, //材质
                'attr_type' => 2
            ],
            'material_color' =>[
                'attr_id' => AttrIdEnum::MATERIAL_COLOR, //材质颜色
                'attr_type' => 2
            ],
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
            'chain_long' => [
                'attr_id' => AttrIdEnum::CHAIN_LENGTH, //链长
                'attr_type' => 1
            ],

            'xiangkou' => [
                'attr_id' => AttrIdEnum::XIANGKOU, //镶口
                'attr_type' => 2
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
            'main_stone_type' => [
                'attr_id' => AttrIdEnum::MAIN_STONE_TYPE, //主石类型
                'attr_type' => 2
            ],
            'diamond_shape' =>[
                'attr_id' => AttrIdEnum::DIA_SHAPE, //钻石形状
                'attr_type' => 2
            ],
            'diamond_carat' => [
                'attr_id' => AttrIdEnum::DIA_CARAT, //钻石大小
                'attr_type' => 1
            ],
            'main_stone_num' => [
                'attr_id' => AttrIdEnum::MAIN_STONE_NUM, //主石数量
                'attr_type' => 1
            ],
            'diamond_color' => [
                'attr_id' => AttrIdEnum::DIA_COLOR, //钻石颜色
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
            'diamond_polish' => [
                'attr_id' => AttrIdEnum::DIA_POLISH, //钻石抛光
                'attr_type' => 2
            ],
            'diamond_symmetry' => [
                'attr_id' => AttrIdEnum::DIA_SYMMETRY, //钻石对称
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



    public function getCostPrice($order_goods){

        if($order_goods->product_type_id == 1){
            //裸钻
            $attr = OrderGoodsAttribute::find()->where(['id'=>$order_goods->id,'attr_id'=>AttrIdEnum::DIA_CERT_NO])->one();
            $cert_id = $attr->attr_value ?? 0;
            $diamond = Diamond::find()->where(['cert_id'=>$cert_id])->one();
            $cost_price = $diamond->cost_price ?? 0;
        }elseif($order_goods->is_stock == IsStockEnum::YES){
            //现货
            $goods_id = $order_goods->goods_id ?? 0;
            $goods = WarehouseGoods::find()->where(['goods_id'=>$goods_id])->one();
            $cost_price = $goods->cost_price ?? 0;
        }elseif ($order_goods->qiban_type == QibanTypeEnum::NON_VERSION){
            //款式库
            $style_sn = $order_goods->style_sn ?? 0;
            $style = Style::find()->where(['style_sn'=>$style_sn])->one();
            $cost_price = $style->cost_price ?? 0;
        }else{
            //起版
            $qiban_sn = $order_goods->qiban_sn ?? 0;
            $qiban = Qiban::find()->where(['qiban_sn'=>$qiban_sn])->one();
            $cost_price = $qiban->cost_price ?? 0;
        }
        return $cost_price;

    }
}


