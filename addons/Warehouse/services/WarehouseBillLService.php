<?php

namespace addons\Warehouse\services;

use addons\Warehouse\common\enums\GoodSourceEnum;
use Yii;
use common\components\Service;
use common\helpers\SnHelper;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Warehouse\common\models\WarehouseBillGoods;
use addons\Warehouse\common\models\WarehouseBillGoodsL;
use addons\Purchase\common\models\PurchaseReceiptGoods;
use addons\Purchase\common\enums\ReceiptGoodsStatusEnum;
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
     * 收货单据汇总
     * @param integer $bill_id
     * @return bool
     */
    public function warehouseBillLSummary($bill_id)
    {
        $result = false;
        $sum = WarehouseBillGoodsL::find()
            ->select(['sum(1) as goods_num', 'sum(cost_price) as total_cost', 'sum(market_price) as total_market'])
            ->where(['bill_id' => $bill_id])
            ->asArray()->one();
        if ($sum) {
            $result = WarehouseBill::updateAll(['goods_num' => $sum['goods_num'] / 1, 'total_cost' => $sum['total_cost'] / 1, 'total_market' => $sum['total_market'] / 1], ['id' => $bill_id]);
        }
        return $result;
    }

    /**
     *
     * 创建收货入库单
     * @param array $bill
     * @param array $goods
     * @return object
     * @throws \Exception
     */
    public function createBillL($bill, $goods)
    {
        $billM = new WarehouseBill();
        $billM->attributes = $bill;
        $billM->bill_no = SnHelper::createBillSn($billM->bill_type);
        if (false === $billM->save()) {
            throw new \Exception($this->getError($billM));
        }
        $bill_id = $billM->attributes['id'];
        $goodsM = new WarehouseBillGoodsL();
        foreach ($goods as $k => &$good) {
            $good['goods_id'] = SnHelper::createGoodsId();
            $good['bill_id'] = $bill_id;
            $good['bill_no'] = $billM->bill_no;
            $good['bill_type'] = $billM->bill_type;
            $goodsM->setAttributes($good);
            if (!$goodsM->validate()) {
                throw new \Exception($this->getError($goodsM));
            }
        }
        $value = [];
        $key = array_keys($goods[0]);
        foreach ($goods as $item) {
            $value[] = array_values($item);
            if (count($value) > 10) {
                $res = Yii::$app->db->createCommand()->batchInsert(WarehouseBillGoodsL::tableName(), $key, $value)->execute();
                if (false === $res) {
                    throw new \Exception("创建收货单据明细失败1");
                }
                $value = [];
            }
        }
        if (!empty($value)) {
            $res = Yii::$app->db->createCommand()->batchInsert(WarehouseBillGoodsL::tableName(), $key, $value)->execute();
            if (false === $res) {
                throw new \Exception("创建收货单据明细失败2");
            }
        }
        return $billM;
    }

    /**
     * 收货入库单审核
     * @param WarehouseBill $form
     * @throws
     */
    public function auditBillL($form)
    {
        if (false === $form->validate()) {
            throw new \Exception($this->getError($form));
        }
        if ($form->audit_status == AuditStatusEnum::PASS) {
            $form->bill_status = BillStatusEnum::CONFIRM;

            $billGoods = WarehouseBillGoodsL::find()->where(['bill_id' => $form->id])->all();
            if (empty($billGoods)) {
                throw new \Exception("单据明细不能为空");
            }
            $bill = WarehouseBill::findOne(['id' => $form->id]);
            $goods = $bill_goods = $goods_ids = [];
            foreach ($billGoods as $good) {
                $goods_ids[] = $good->goods_id;
                $good  = new WarehouseBillGoodsL();
                $goods[] = [
                    //基本信息
                    'goods_id' => $good->goods_id,//条码号
                    'goods_name' => $good->goods_name,//商品名称
                    'goods_image' => $good->goods_image,//商品图片
                    'style_sn' => $good->style_sn,//款号
                    'style_cate_id' => $good->style_cate_id,//产品分类
                    'product_type_id' => $good->product_type_id,//产品线
                    'style_sex' => $good->style_sex,//款式性别
                    'style_channel_id' => $good->style_channel_id,//款式渠道
                    'qiban_sn' => $good->qiban_sn,//起版号
                    'qiban_type' => $good->qiban_type,//起版类型
                    'goods_num' => $good->goods_num,//商品数量
                    'goods_status' => GoodsStatusEnum::IN_STOCK,//库存状态
                    'goods_source' => GoodSourceEnum::QUICK_STORAGE,//入库存方式
                    'supplier_id' => $bill->supplier_id,//供应商
                    'put_in_type' => $bill->put_in_type,//入库方式
                    'company_id' => 1,//所在公司(默认1)
                    'warehouse_id' => $bill->to_warehouse_id ?: 0,//入库仓库
                    'order_sn' => $good->order_sn ?? "",//订单号
                    'order_detail_id' => (string)$good->order_detail_id ?? "",//订单明细ID
                    'produce_sn' => $good->produce_sn,//布产号

                    //属性信息
                    'material' => $good->material,//主成色
                    'material_type' => $good->material_type,//材质
                    'material_color' => $good->material_color,//材质颜色
                    'xiangkou' => $good->xiangkou,//戒托镶口
                    'finger' => $good->finger,//手寸(美号)
                    'finger_hk' => $good->finger_hk,//手寸(港号)
                    'length' => $good->length,//尺寸
                    'product_size' => $good->product_size,//成品尺寸
                    'chain_long' => $good->chain_long,//链长
                    'chain_type' => $good->chain_type,//链类型
                    'cramp_ring' => $good->cramp_ring,//扣环
                    'talon_head_type' => $good->talon_head_type,//爪头形状
                    'kezi' => $good->kezi,//刻字

                    //配件信息
                    'peijian_way' => $good->parts_way,//配件方式
                    'peijian_type' => $good->parts_type,//配件类型
                    //'peijian_cate' => $good->parts_way,
                    'parts_num' => $good->parts_num,//配件数量
                    'parts_material' => $good->parts_material,//配件材质
                    'parts_gold_weight' => $good->parts_gold_weight,//配件金重
                    'parts_price' => $good->parts_price,//配件金料单价
                    'parts_amount' => $good->parts_amount,//配件成本

                    //金料信息
                    'peiliao_way' => $good->peiliao_way,//配料方式
                    'gold_weight' => $good->gold_weight,//金重
                    'suttle_weight' => $good->suttle_weight,//净重(连石重)
                    'gold_loss' => $good->gold_loss,
                    'gold_price' => $good->gold_price,
                    'gold_amount' => $good->gold_amount,
                    'gross_weight' => $good->gross_weight,
                    'pure_gold' => $good->pure_gold,

                    //钻石信息
//                    'diamond_carat' => $good->diamond_carat,
//                    'diamond_clarity' => $good->diamond_clarity,
//                    'diamond_cut' => $good->diamond_cut,
//                    'diamond_shape' => $good->diamond_shape,
//                    'diamond_color' => $good->diamond_color,
                    'diamond_polish' => $good->diamond_polish,
                    'diamond_symmetry' => $good->diamond_symmetry,
                    'diamond_fluorescence' => $good->diamond_fluorescence,
                    'diamond_discount' => $good->diamond_discount,
//                    'diamond_cert_type' => $good->diamond_cert_type,
//                    'diamond_cert_id' => $good->diamond_cert_id,

                    //主石
                    'main_peishi_way' => $good->main_pei_type,
                    //'main_peishi_type' => $good->main_pei_type,
                    'main_stone_sn' => $good->main_stone_sn,
                    'main_stone_type' => $good->main_stone_type,
                    'main_stone_num' => $good->main_stone_num,
                    'main_stone_price' => $good->main_stone_price,
                    'main_stone_colour' => $good->main_stone_colour,
                    'main_stone_size' => $good->main_stone_size,
                    'main_stone_cost' => $good->main_stone_amount,
                    //-----------------------------------
                    'diamond_carat' => $good->main_stone_weight,
                    'diamond_clarity' => $good->main_stone_clarity,
                    'diamond_cut' => $good->main_stone_cut,
                    'diamond_shape' => $good->diamond_shape,
                    'diamond_color' => $good->diamond_color,
                    'diamond_cert_type' => $good->main_cert_type,
                    'diamond_cert_id' => $good->main_cert_id,

                    //副石1
                    'second_peishi_way1' => $good->second_pei_type,
                    'second_stone_sn1' => $good->second_stone_sn1,
                    'second_stone_type1' => $good->second_stone_type1,
                    'second_stone_num1' => $good->second_stone_num1,
                    'second_stone_weight1' => $good->second_stone_weight1,
                    'second_stone_price1' => $good->second_stone_price1,
                    'second_stone_color1' => $good->second_stone_color1,
                    'second_stone_clarity1' => $good->second_stone_clarity1,
                    'second_stone_shape1' => $good->second_stone_shape1,
                    'second_stone_size1' => $good->second_stone_size1,
                    'second_stone_colour1' => $good->second_stone_colour1,
                    'second_cert_id1' => $good->second_cert_id1,
                    'second_stone1_cost' => $good->second_stone_amount1,


                    //副石2
                    'second_peishi_type2' => $good->second_pei_type2,
                    'second_stone_sn2' => $good->second_stone_sn2,
                    'second_stone_type2' => $good->second_stone_type2,
                    'second_stone_num2' => $good->second_stone_num2,
                    'second_stone_weight2' => $good->second_stone_weight2,
                    'second_stone_price2' => $good->second_stone_price2,
                    'second_stone_color2' => $good->second_stone_color2,
                    'second_stone_clarity2' => $good->second_stone_clarity2,
                    'second_stone_shape2' => $good->second_stone_shape2,
                    'second_stone_size2' => $good->second_stone_size2,
                    //'second_stone_colour2' => $good->second_stone_colour2,
                    'second_stone2_cost' => $good->second_stone_amount2,

                    //副石3
                    'second_peishi_way3' => $good->second_pei_type3,
                    'second_stone_type3' => $good->second_stone_type3,
                    'second_stone_num3' => $good->second_stone_num3,
                    'second_stone_weight3' => $good->second_stone_weight3,
                    'second_stone_price3' => $good->second_stone_price3,
                    'shiliao_remark' => $good->stone_remark,

                    //工费信息
                    'ke_gong_fee' => $good->gong_fee,
                    'piece_fee' => $good->piece_fee,
                    'peishi_fee' => $good->peishi_gong_fee,
                    'peishi_amount' => $good->peishi_fee,
                    'parts_fee' => $good->parts_fee,
                    'bukou_fee' => $good->bukou_fee,
                    'xianqian_price' => $good->xianqian_price,
                    'biaomiangongyi_fee' => $good->biaomiangongyi_fee,
                    'xianqian_fee' => $good->xianqian_fee,
                    'gong_fee' => $good->gong_fee,
                    'penrasa_fee' => $good->penlasha_fee,
                    'lasha_fee' => $good->lasha_fee,
                    'edition_fee' => $good->templet_fee,
                    'total_gong_fee' => $good->total_gong_fee,

                    //价格信息
                    'cost_price' => $good->cost_price,
                    'market_price' => $good->market_price,
                    'markup_rate' => $good->markup_rate,

                    //其他
                    'cert_id' => $good->cert_id,
                    'cert_type' => $good->cert_type,
                    'jintuo_type' => $good->jintuo_type,
                    'xiangqian_craft' => $good->xiangqian_craft,
                    'biaomiangongyi' => $good->biaomiangongyi,
                    'is_inlay' => $good->is_inlay,
                    'factory_mo' => $good->factory_mo,
                    'remark' => $good->remark,
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
                if (!$model->validate()) {
                    throw new \Exception($this->getError($model));
                }
                $value[] = array_values($item);
                if (count($value) >= 10) {
                    $res = Yii::$app->db->createCommand()->batchInsert(WarehouseGoods::tableName(), $key, $value)->execute();
                    if (false === $res) {
                        throw new \Exception("创建货品信息失败[code=1]");
                    }
                    $value = [];
                }
            }
            if (!empty($value)) {
                $res = Yii::$app->db->createCommand()->batchInsert(WarehouseGoods::tableName(), $key, $value)->execute();
                if (false === $res) {
                    throw new \Exception("创建货品信息失败[code=2]");
                }
            }
            $value = [];
            $key = array_keys($bill_goods[0]);
            foreach ($bill_goods as $item) {
                $goodsM->setAttributes($item);
                if (!$goodsM->validate()) {
                    throw new \Exception($this->getError($goodsM));
                }
                $value[] = array_values($item);
                if (count($value) >= 10) {
                    $res = Yii::$app->db->createCommand()->batchInsert(WarehouseBillGoods::tableName(), $key, $value)->execute();
                    if (false === $res) {
                        throw new \Exception("创建收货单明细失败[code=1]");
                    }
                }
            }
            if (!empty($value)) {
                $res = Yii::$app->db->createCommand()->batchInsert(WarehouseBillGoods::tableName(), $key, $value)->execute();
                if (false === $res) {
                    throw new \Exception("创建收货单明细失败[code=2]");
                }
            }
            //创建货号
            $ids = WarehouseGoods::find()->select(['id'])->where(['goods_id' => $goods_ids])->all();
            $ids = ArrayHelper::getColumn($ids, 'id');
            if ($ids) {
                foreach ($ids as $id) {
                    $goods = WarehouseGoods::findOne(['id' => $id]);
                    $old_goods_id = $goods->goods_id;
                    $goodsL = WarehouseBillGoodsL::findOne(['goods_id' => $old_goods_id]);
                    if (!$goodsL->auto_goods_id) {
                        $goods_id = \Yii::$app->warehouseService->warehouseGoods->createGoodsId($goods);
                        $bGoodsM = WarehouseBillGoods::findOne(['goods_id' => $old_goods_id]);
                        $bGoodsM->goods_id = $goods_id;
                        if (false === $bGoodsM->save(true, ['id', 'goods_id'])) {
                            throw new \Exception($this->getError($bGoodsM));
                        }
                        $goodsL->goods_id = $goods_id;
                        if (false === $goodsL->save(true, ['id', 'goods_id'])) {
                            throw new \Exception($this->getError($goodsL));
                        }
                    }
                }
            }
            if ($form->order_type == OrderTypeEnum::ORDER_L
                && $form->audit_status == AuditStatusEnum::PASS) {
                //同步采购收货单货品状态
                $ids = ArrayHelper::getColumn($billGoods, 'source_detail_id');
                if ($ids) {
                    $res = PurchaseReceiptGoods::updateAll(['goods_status' => ReceiptGoodsStatusEnum::WAREHOUSE], ['id' => $ids]);
                    if (false === $res) {
                        throw new \Exception("同步采购收货单货品状态失败");
                    }
                }
            }
        } else {
            $form->bill_status = BillStatusEnum::SAVE;
        }
        if (false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
    }

}