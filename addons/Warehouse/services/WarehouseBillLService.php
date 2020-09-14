<?php

namespace addons\Warehouse\services;

use addons\Style\common\enums\JintuoTypeEnum;
use addons\Warehouse\common\enums\GoodSourceEnum;
use common\enums\LogTypeEnum;
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
                //$good  = new WarehouseBillGoodsL();
                if (empty($good->goods_name)) {
                    $good->goods_name = "待定";
                }
                if (empty($good->jintuo_type)) {
                    $good->jintuo_type = JintuoTypeEnum::Chengpin;
                }
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
                    'goods_source' => GoodSourceEnum::QUICK_STORAGE,//写入库存方式
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
                    'xiangqian_craft' => $good->xiangqian_craft,//镶嵌工艺
                    'biaomiangongyi' => $good->biaomiangongyi,//表面工艺
                    'kezi' => $good->kezi,//刻字
                    'goods_color' => $good->goods_color,//货品外部颜色
                    'cert_id' => $good->cert_id,//成品证书号
                    'cert_type' => $good->cert_type,//证书类别[成品]
                    'jintuo_type' => $good->jintuo_type,//金托类型

                    //金料信息
                    'peiliao_way' => $good->peiliao_way,//配料方式
                    'gold_weight' => $good->gold_weight,//金重
                    'suttle_weight' => $good->suttle_weight,//净重(连石重)
                    'gross_weight' => $good->lncl_loss_weight,//毛重(含耗重)
                    'gold_loss' => $good->gold_loss,//损耗
                    'pure_gold' => $good->pure_gold,//折足
                    'gold_price' => $good->gold_price,//金价
                    'gold_amount' => $good->gold_amount,//金料成本

                    //钻石信息
//                    'diamond_carat' => $good->diamond_carat,
//                    'diamond_clarity' => $good->diamond_clarity,
//                    'diamond_cut' => $good->diamond_cut,
//                    'diamond_shape' => $good->diamond_shape,
//                    'diamond_color' => $good->diamond_color,
                    'diamond_polish' => $good->diamond_polish,//钻石抛光
                    'diamond_symmetry' => $good->diamond_symmetry,//钻石对称
                    'diamond_fluorescence' => $good->diamond_fluorescence,//钻石荧光
                    'diamond_discount' => $good->diamond_discount,//钻石折扣
//                    'diamond_cert_type' => $good->diamond_cert_type,
//                    'diamond_cert_id' => $good->diamond_cert_id,

                    //主石
                    'main_peishi_way' => $good->main_pei_type,//主石配石方式
                    //'main_peishi_type' => $good->main_pei_type,
                    'main_stone_sn' => $good->main_stone_sn,//主石编号
                    'main_stone_type' => $good->main_stone_type,//主石类型
                    'main_stone_num' => $good->main_stone_num,//主石粒数
                    //'main_stone_weight' => $good->main_stone_weight,//主石重
                    //'main_stone_shape' => $good->main_stone_shape,//主石形状
                    //'main_stone_color' => $good->main_stone_color,//主石颜色
                    //'main_stone_clarity' => $good->main_stone_clarity,//主石净度
                    //'main_stone_cut' => $good->main_stone_cut,//主石切工
                    'main_stone_colour' => $good->main_stone_colour,//主石色彩
                    'main_stone_size' => $good->main_stone_size,//主石规格
                    //'main_cert_id' => $good->main_cert_type,//主石证书号
                    //'main_cert_type' => $good->main_cert_id,//主石证书类型
                    'main_stone_price' => $good->main_cert_type,//主石单价
                    'main_stone_cost' => $good->main_stone_amount,//主石成本价
                    //-----------------------------------//差异
                    'diamond_carat' => $good->main_stone_weight,//主石重
                    'diamond_shape' => $good->main_stone_shape,//主石形状
                    'diamond_color' => $good->main_stone_color,//主石颜色
                    'diamond_clarity' => $good->main_stone_clarity,//主石净度
                    'diamond_cut' => $good->main_stone_cut,//主石切工
                    'diamond_cert_id' => $good->main_cert_id,//主石证书号
                    'diamond_cert_type' => $good->main_cert_type,//主石证书类型

                    //副石1
                    'second_peishi_way1' => $good->second_pei_type,//副石1配石方式
                    'second_stone_sn1' => $good->second_stone_sn1,//副石1编号
                    'second_stone_type1' => $good->second_stone_type1,//副石1类型
                    'second_stone_num1' => $good->second_stone_num1,//副石1粒数
                    'second_stone_weight1' => $good->second_stone_weight1,//副石1重
                    'second_stone_shape1' => $good->second_stone_shape1,//副石1形状
                    'second_stone_color1' => $good->second_stone_color1,//副石1颜色
                    'second_stone_clarity1' => $good->second_stone_clarity1,//副石1净度
                    //'second_stone_cut1' => $good->second_stone_cut1,//副石1切工
                    'second_stone_colour1' => $good->second_stone_colour1,//副石1色彩
                    'second_stone_size1' => $good->second_stone_size1,//副石1规格
                    'second_cert_id1' => $good->second_cert_id1,//副石1证书号
                    'second_stone_price1' => $good->second_stone_price1,//副石1单价
                    'second_stone_cost1' => $good->second_stone_amount1,//副石1成本价

                    //副石2
                    'second_peishi_type2' => $good->second_pei_type2,//副石2配石方式
                    'second_stone_sn2' => $good->second_stone_sn2,//副石2编号
                    'second_stone_type2' => $good->second_stone_type2,//副石2类型
                    'second_stone_num2' => $good->second_stone_num2,//副石2粒数
                    'second_stone_weight2' => $good->second_stone_weight2,//副石2重
                    'second_stone_shape2' => $good->second_stone_shape2,//副石2形状
                    'second_stone_color2' => $good->second_stone_color2,//副石2颜色
                    'second_stone_clarity2' => $good->second_stone_clarity2,//副石2净度
                    //'second_stone_colour2' => $good->second_stone_colour2,//副石2色彩
                    'second_stone_size2' => $good->second_stone_size2,//副石2规格
                    //'second_cert_id2' => $good->second_cert_id2,//副石2证书号
                    'second_stone_price2' => $good->second_stone_price2,//副石2单价
                    'second_stone_cost2' => $good->second_stone_amount2,//副石2成本价

                    //副石3
                    'second_peishi_way3' => $good->second_pei_type3,//副石3配石方式
                    'second_stone_sn3' => $good->second_stone_sn3,//副石3编号
                    'second_stone_type3' => $good->second_stone_type3,//副石3类型
                    'second_stone_num3' => $good->second_stone_num3,//副石3粒数
                    'second_stone_weight3' => $good->second_stone_weight3,//副石3重量
                    'second_stone_price3' => $good->second_stone_price3,//副石3单价
                    'second_stone_cost3' => $good->second_stone_amount3,//副石3成本价
                    'shiliao_remark' => $good->stone_remark,

                    //配件信息
                    'peijian_way' => $good->parts_way,//配件方式
                    'peijian_type' => $good->parts_type,//配件类型
                    //'peijian_cate' => $good->parts_way,
                    'parts_num' => $good->parts_num,//配件数量
                    'parts_material' => $good->parts_material,//配件材质
                    'parts_gold_weight' => $good->parts_gold_weight,//配件金重
                    'parts_price' => $good->parts_price,//配件金价
                    'parts_amount' => $good->parts_amount,//配件成本

                    //工费信息
                    'ke_gong_fee' => $good->gong_fee,//克/工费
                    'piece_fee' => $good->piece_fee,//件/工费
                    'gong_fee' => $good->basic_gong_fee,//基本工费
                    //'peishi_num' => $good->peishi_num,//配石数量
                    'peishi_weight' => $good->peishi_weight,//配石重量
                    'peishi_fee' => $good->peishi_gong_fee,//配石工费
                    'peishi_amount' => $good->peishi_fee,//配石费
                    'xianqian_price' => $good->xianqian_price,//镶石单价/颗
                    'xianqian_fee' => $good->xianqian_fee,//镶石费
                    'second_stone_fee1' => $good->second_stone_fee1,//镶石1工费
                    'second_stone_fee2' => $good->second_stone_fee2,//镶石2工费
                    'second_stone_fee3' => $good->second_stone_fee3,//镶石3工费
                    'parts_fee' => $good->parts_fee,//配件工费
                    'edition_fee' => $good->templet_fee,//版费
                    'penrasa_fee' => $good->penlasha_fee,//喷沙费
                    'lasha_fee' => $good->lasha_fee,//拉沙费
                    'bukou_fee' => $good->bukou_fee,//补扣费
                    'extra_stone_fee' => $good->extra_stone_fee,//超石费
                    'fense_fee' => $good->fense_fee,//分色/分件费
                    'biaomiangongyi_fee' => $good->biaomiangongyi_fee,//表面工艺费
                    'tax_fee' => $good->tax_fee,//税费
                    'cert_fee' => $good->cert_fee,//证书费
                    'other_fee' => $good->other_fee,//其它工费
                    'total_gong_fee' => $good->total_gong_fee,//总工费

                    //价格信息
                    'factory_cost' => $good->factory_cost,//工厂成本
                    'markup_rate' => $good->markup_rate,//加价率(倍率)
                    'market_price' => $good->market_price,//市场价(标签价)
                    'cost_price' => $good->cost_price,//公司成本价

                    //其他
                    'factory_mo' => $good->factory_mo,//模号
                    'is_inlay' => $good->is_inlay,//是否镶嵌
                    'remark' => $good->remark,//备注
                    'creator_id' => \Yii::$app->user->identity->getId(),
                    'created_at' => time(),
                ];
                $bill_goods[] = [
                    'bill_id' => $good->bill_id,//单据ID
                    'bill_no' => $bill->bill_no,//单据编号
                    'bill_type' => $bill->bill_type,//单据类型
                    'goods_id' => $good->goods_id,//货号
                    'goods_name' => $good->goods_name,//商品名称
                    'style_sn' => $good->style_sn,//款式编号
                    'goods_num' => $good->goods_num,//商品数量
                    'put_in_type' => $bill->put_in_type,//入库方式
                    'cost_price' => $good->cost_price,//成本价
                    //'sale_price' => $good->sale_price,//销售价
                    //'market_price' => $good->market_price,//市场价
                    'status' => StatusEnum::ENABLED,//状态
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

                    //插入商品日志
                    $log = [
                        'goods_id' => $id,
                        'goods_status' => GoodsStatusEnum::IN_STOCK,
                        'log_type' => LogTypeEnum::ARTIFICIAL,
                        'log_msg' => '入库单：'.$form->bill_no.";货品状态:“".GoodsStatusEnum::getValue(GoodsStatusEnum::IN_STOCK)."”"
                    ];
                    Yii::$app->warehouseService->goodsLog->createGoodsLog($log);
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