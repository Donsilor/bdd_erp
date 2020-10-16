<?php

namespace addons\Warehouse\services;

use Yii;
use common\components\Service;
use common\helpers\SnHelper;
use addons\Warehouse\common\models\WarehouseGoods;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseBillGoodsL;
use addons\Warehouse\common\models\WarehouseStone;
use addons\Warehouse\common\forms\WarehouseBillTForm;
use addons\Warehouse\common\forms\WarehouseBillTGoodsForm;
use addons\Style\common\models\Style;
use addons\Style\common\models\Qiban;
use addons\Warehouse\common\enums\PeiJianWayEnum;
use addons\Warehouse\common\enums\PeiLiaoWayEnum;
use addons\Warehouse\common\enums\PeiShiWayEnum;
use addons\Warehouse\common\enums\IsWholeSaleEnum;
use addons\Style\common\enums\StonePositionEnum;
use addons\Style\common\enums\JintuoTypeEnum;
use addons\Style\common\enums\QibanTypeEnum;
use addons\Style\common\enums\AttrIdEnum;
use addons\Style\common\enums\InlayEnum;
use common\enums\AuditStatusEnum;
use common\helpers\UploadHelper;
use common\enums\StatusEnum;
use common\helpers\StringHelper;
use common\enums\ConfirmEnum;
use yii\helpers\Url;

/**
 * 其它收货单
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseBillTService extends Service
{

    /**
     * 单据汇总
     * @param int $bill_id
     * @return bool
     * @throws
     */
    public function warehouseBillTSummary($bill_id)
    {
        $result = false;
        $sum = WarehouseBillGoodsL::find()
            ->select(['sum(goods_num) as goods_num', 'sum(cost_amount) as total_cost', 'sum(market_price) as total_market'])
            ->where(['bill_id' => $bill_id])
            ->asArray()->one();
        if ($sum) {
            $result = WarehouseBill::updateAll(['goods_num' => $sum['goods_num'] / 1, 'total_cost' => $sum['total_cost'] / 1, 'total_market' => $sum['total_market'] / 1], ['id' => $bill_id]);
        }
        return $result;
    }

    /**
     * 添加明细
     * @param WarehouseBillTGoodsForm $form
     * @throws
     */
    public function addBillTGoods($form)
    {

        if (!$form->goods_sn) {
            throw new \Exception("款号/起版号不能为空");
        }
        if (!$form->goods_num) {
            throw new \Exception("商品数量必填");
        }
        if (!is_numeric($form->goods_num)) {
            throw new \Exception("商品数量不合法");
        }
        if ($form->goods_num <= 0) {
            throw new \Exception("商品数量必须大于0");
        }
        if ($form->goods_num > 100) {
            throw new \Exception("一次最多只能添加100个商品，可分多次添加");
        }
        $goods_num = 1;
        if ($form->is_wholesale) {//批发
            $goods_num = $form->goods_num;
            $form->goods_num = 1;
        }
        $form->is_auto_price = ConfirmEnum::NO;
        if ($form->cost_price) {//自动计算成本
            $form->is_auto_price = ConfirmEnum::YES;
        }
        $style = Style::find()->where(['style_sn' => $form->goods_sn])->one();
        if (!$style) {
            $qiban = Qiban::find()->where(['qiban_sn' => $form->goods_sn])->one();
            if (!$qiban) {
                throw new \Exception("[款号/起版号]不存在");
            } elseif ($qiban->status != StatusEnum::ENABLED) {
                throw new \Exception("起版号不可用");
            } else {
                $exist = WarehouseBillGoodsL::find()->where(['bill_id' => $form->bill_id, 'qiban_sn' => $form->goods_sn, 'status' => StatusEnum::ENABLED])->count();
                if ($exist) {
                    //throw new \Exception("起版号已添加过");
                }
                if ($form->cost_price) {
                    $qiban->cost_price = $form->cost_price;
                }
                //$qiban = new Qiban();
                $goods = [
                    'goods_sn' => $form->goods_sn,
                    'goods_name' => $qiban->qiban_name,
                    'style_id' => $qiban->id,
                    'style_sn' => $form->goods_sn,
                    'goods_image' => $style->style_image,
                    'qiban_type' => $qiban->qiban_type,
                    'product_type_id' => $qiban->product_type_id,
                    'style_cate_id' => $qiban->style_cate_id,
                    'style_channel_id' => $qiban->style_channel_id,
                    'style_sex' => $qiban->style_sex,
                    'goods_num' => $goods_num,
                    'jintuo_type' => $qiban->jintuo_type,
                    'cost_price' => bcmul($qiban->cost_price, $goods_num, 3),
                    //'market_price' => $style->market_price,
                    'is_inlay' => $qiban->is_inlay,
                    'remark' => $qiban->remark,
                    'creator_id' => \Yii::$app->user->identity->getId(),
                    'created_at' => time(),
                ];
            }
        } elseif ($style->status != StatusEnum::ENABLED) {
            throw new \Exception("款号不可用");
        } else {
            if ($form->cost_price) {
                $style->cost_price = $form->cost_price;
            }
            //$style = new Style();
            $goods = [
                'goods_sn' => $form->goods_sn,
                'goods_name' => $style->style_name,
                'style_id' => $style->id,
                'style_sn' => $form->goods_sn,
                'goods_image' => $style->style_image,
                'qiban_type' => QibanTypeEnum::NON_VERSION,
                'product_type_id' => $style->product_type_id,
                'style_cate_id' => $style->style_cate_id,
                'style_channel_id' => $style->style_channel_id,
                'style_sex' => $style->style_sex,
                'goods_num' => $goods_num,
                'jintuo_type' => JintuoTypeEnum::Chengpin,
                'cost_price' => bcmul($style->cost_price, $goods_num, 3),
                'is_inlay' => $style->is_inlay,
                //'market_price' => $style->market_price,
                'creator_id' => \Yii::$app->user->identity->getId(),
                'created_at' => time(),
            ];
        }
        $bill = WarehouseBill::findOne(['id' => $form->bill_id]);
        $goodsM = new WarehouseBillGoodsL();
        $goodsInfo = [];
        for ($i = 0; $i < $form->goods_num; $i++) {
            $goodsInfo[$i] = $goods;
            $goodsInfo[$i]['bill_id'] = $form->bill_id;
            $goodsInfo[$i]['bill_no'] = $bill->bill_no;
            $goodsInfo[$i]['bill_type'] = $bill->bill_type;
            $goodsInfo[$i]['goods_id'] = SnHelper::createGoodsId();
            $goodsInfo[$i]['to_warehouse_id'] = $form->to_warehouse_id;
            $goodsInfo[$i]['is_wholesale'] = $form->is_wholesale;//批发
            $goodsInfo[$i]['auto_goods_id'] = $form->auto_goods_id;
            $goodsInfo[$i]['is_auto_price'] = $form->is_auto_price;
            $goodsM->setAttributes($goodsInfo[$i]);
            if (!$goodsM->validate()) {
                throw new \Exception($this->getError($goodsM));
            }
        }
        $value = [];
        $key = array_keys($goodsInfo[0]);
        foreach ($goodsInfo as $item) {
            $value[] = array_values($item);
            if (count($value) >= 10) {
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

        $this->warehouseBillTSummary($form->bill_id);
    }

    /**
     * 批量导入
     * @param WarehouseBillTGoodsForm $form
     * @throws
     */
    public function uploadGoods($form)
    {
        if (empty($form->file) && !isset($form->file)) {
            throw new \Exception("请上传文件");
        }
        if (UploadHelper::getExt($form->file->name) != 'csv') {
            throw new \Exception("请上传csv格式文件");
        }
        if (!$form->file->tempName) {
            throw new \Exception("文件不能为空");
        }
        $file = fopen($form->file->tempName, 'r');
        $i = 0;
        $flag = true;
        $error_off = true;
        $error = $saveData = $goods_ids = $style_sns = [];
        $bill = WarehouseBill::findOne($form->bill_id);
        $warehouseAll = $form->getWarehouseMap();
        while ($goods = fgetcsv($file)) {
            if ($i <= 1) {
                $i++;
                continue;
            }
            if (count($goods) != 106) {
                throw new \Exception("模板格式不正确，请下载最新模板");
            }
            $goods = $form->trimField($goods);
            $goods_id = $goods['goods_id'] ?? "";//条码号
            $auto_goods_id = ConfirmEnum::YES;//是否自动货号 默认手填
            if (empty($goods_id)) {
                $goods_id = SnHelper::createGoodsId();
                $auto_goods_id = ConfirmEnum::NO;
            } else {
                if ($key = array_search($goods_id, $goods_ids)) {
                    $flag = false;
                    $error[$i][] = "货号与第" . ($key + 1) . "行货号重复";
                }
                $goods_ids[$i] = $goods_id;

                $exist_goods_id = WarehouseGoods::findOne(['goods_id' => $goods_id]);
                if (!empty($exist_goods_id)) {
                    $flag = false;
                    $error[$i][] = "货号在库存中已存在";
                }
            }
            $style_sn = $goods['style_sn'] ?? "";//款号
            $jintuo_type = $goods['jintuo_type'] ?? "";//金托类型
            if (!empty($jintuo_type)) {
                $jintuo_type = JintuoTypeEnum::getIdByName($jintuo_type);
                if (empty($jintuo_type)) {
                    $flag = false;
                    $error[$i][] = "金托类型：录入值有误";
                    $jintuo_type = "";
                }
            } else {
//                $flag = false;
//                $error[$i][] = "金托类型不能为空";
                $jintuo_type = JintuoTypeEnum::Chengpin;
            }
            $qiban_sn = $goods['qiban_sn'] ?? "";//起版号
            if (!empty($style_sn)) {
                $style_sns[$i] = "【" . $style_sn . "】";
            } else {
                $style_sns[$i] = "【" . $qiban_sn . "】";
            }
            if (!empty($style_sn) && !empty($qiban_sn)) {
                //throw new \Exception($row . "[款号]和[起版号]只能填其一");
            }
            $qiban_type = QibanTypeEnum::NON_VERSION;
            if (!empty($qiban_sn)) {
                $qiban = Qiban::findOne(['qiban_sn' => $qiban_sn]);
                if (!$qiban) {
                    $flag = false;
                    $error[$i][] = "[起版号]不存在";
                } elseif ($qiban->status != StatusEnum::ENABLED) {
                    $flag = false;
                    $error[$i][] = "[起版号]未启用";
                } elseif (empty($qiban->style_sn)) {
                    $qiban_type = QibanTypeEnum::NO_STYLE;
                } else {
                    if (!empty($style_sn)
                        && $style_sn != $qiban->style_sn) {
                        $flag = false;
                        $error[$i][] = "有空起版[款号]和填写[款号]不一致";
                    }
                    $qiban_type = QibanTypeEnum::HAVE_STYLE;
                }
                $style_sn = $qiban->style_sn ?? "";
            }
            $is_inlay = InlayEnum::No;
            if ($qiban_type != QibanTypeEnum::NO_STYLE) {
                if (empty($style_sn)) {
                    $flag = false;
                    $error[$i][] = "款号不能为空";
                    if (!$flag) {
                        $i++;
                        continue;
                    }
                }
                $qibanType = QibanTypeEnum::getMap();
                $qiban_error = $qibanType[$qiban_type] ?? "";
                $style = Style::findOne(['style_sn' => $style_sn]);
                if (empty($style)) {
                    $flag = false;
                    $error[$i][] = $qiban_error . "[款号]不存在";
                    if (!$flag) {
                        $i++;
                        continue;
                    }
                }
                if ($style->audit_status != AuditStatusEnum::PASS) {
                    $flag = false;
                    $error[$i][] = $qiban_error . "[款号]未审核";
                }
                if ($style->status != StatusEnum::ENABLED) {
                    $flag = false;
                    $error[$i][] = $qiban_error . "[款号]不是启用状态";
                }
                if ($style->type) {
                    $is_inlay = $style->type->is_inlay;
                }
                $is_inlay = $is_inlay ?? InlayEnum::No;
            }
            if (!$flag) {
                //$flag = true;
                //continue;
            }
            if (!empty($qiban_sn)) {
                $style_image = $qiban->style_image;
                $style_cate_id = $qiban->style_cate_id;
                $product_type_id = $qiban->product_type_id;
                $style_sex = $qiban->style_sex;
                $style_channel_id = $qiban->style_channel_id;
            } else {
                $style_image = $style->style_image;
                $style_cate_id = $style->style_cate_id;
                $product_type_id = $style->product_type_id;
                $style_sex = $style->style_sex;
                $style_channel_id = $style->style_channel_id;
            }
            $goods_sn = !empty($style_sn) ? $style_sn : $qiban_sn;
            $goods_name = $goods['goods_name'] ?? "";//货品名称
            $to_warehouse_id = $goods['to_warehouse_id'] ?? "";//入库仓库
            if (!empty($to_warehouse_id)) {
                $to_warehouse_id = $form->getWarehouseId($to_warehouse_id, $warehouseAll, 0);
                if (empty($to_warehouse_id)) {
                    $flag = false;
                    $error[$i][] = "入库仓库：[" . $to_warehouse_id . "]录入值有误";
                    $to_warehouse_id = "";
                }
            } else {
                $flag = false;
                $error[$i][] = "入库仓库不能为空";
            }
            $material_type = $goods['material_type'] ?? "";//材质
            if (!empty($material_type)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $material_type, AttrIdEnum::MATERIAL_TYPE);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "材质：[" . $material_type . "]录入值有误";
                    $material_type = "";
                } else {
                    $material_type = $attr_id;
                }
            }
            $material_color = $goods['material_color'] ?? "";//材质颜色
            if (!empty($material_color)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $material_color, AttrIdEnum::MATERIAL_COLOR);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "材质颜色：[" . $material_color . "]录入值有误";
                    $material_color = "";
                } else {
                    $material_color = $attr_id;
                }
            }
            $goods_num = $form->formatValue($goods['goods_num'], 1) ?? 1;//货品数量
            $is_wholesale = IsWholeSaleEnum::NO;
            if ($goods_num > 1) {
                $is_wholesale = IsWholeSaleEnum::YES;
            }
            $finger_hk = $goods['finger_hk'] ?? "";//手寸(港号)
            if (!empty($finger_hk)) {
                $finger_hk = StringHelper::findNum($finger_hk);
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $finger_hk, AttrIdEnum::FINGER_HK);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "手寸(港号)：[" . $finger_hk . "]录入值有误";
                    $finger_hk = "";
                } else {
                    $finger_hk = $attr_id;
                }
            }
            $finger = $goods['finger'] ?? "";//手寸(美号)
            if (!empty($finger)) {
                $finger = StringHelper::findNum($finger);
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $finger, AttrIdEnum::FINGER);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "手寸(美号)：[" . $finger . "]录入值有误";
                    $finger = "";
                } else {
                    $finger = $attr_id;
                }
            }
            $length = $goods['length'] ?? "";//尺寸
            $product_size = $goods['product_size'] ?? "";//成品尺寸
            $xiangkou = $goods['xiangkou'] ?? "";//镶口
            if (!empty($xiangkou)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $xiangkou, AttrIdEnum::XIANGKOU);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "镶口：[" . $xiangkou . "]录入值有误";
                    $xiangkou = "";
                } else {
                    $xiangkou = $attr_id;
                }
            }
            $kezi = $goods['kezi'] ?? "";//刻字
            $chain_type = $goods['chain_type'] ?? "";//链类型
            if (!empty($chain_type)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $chain_type, AttrIdEnum::CHAIN_TYPE);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "链类型：[" . $chain_type . "]录入值有误";
                    $chain_type = "";
                } else {
                    $chain_type = $attr_id;
                }
            }
            $cramp_ring = $goods['cramp_ring'] ?? "";//扣环
            if (!empty($cramp_ring)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $cramp_ring, AttrIdEnum::CHAIN_BUCKLE);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "扣环：[" . $cramp_ring . "]录入值有误";
                    $cramp_ring = "";
                } else {
                    $cramp_ring = $attr_id;
                }
            }
            $talon_head_type = $goods['talon_head_type'] ?? "";//爪头形状
            if (!empty($talon_head_type)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $talon_head_type, AttrIdEnum::TALON_HEAD_TYPE);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "爪头形状：[" . $talon_head_type . "]录入值有误";
                    $talon_head_type = "";
                } else {
                    $talon_head_type = $attr_id;
                }
            }

            $peiliao_way = $form->formatValue($goods['peiliao_way'], 0) ?? "";//配料方式
            if (!empty($peiliao_way)) {
                $peiliao_way = \addons\Warehouse\common\enums\PeiLiaoWayEnum::getIdByName($peiliao_way);
                if (empty($peiliao_way) && $peiliao_way === "") {
                    $flag = false;
                    $error[$i][] = "配料方式：[" . $peiliao_way . "]录入值有误";
                    $peiliao_way = 0;
                }
            }
            $suttle_weight = $form->formatValue($goods['suttle_weight'], 0) ?? 0;//连石重(净重)
            $gold_loss = $form->formatValue($goods['gold_loss'], 0) ?? 0;//耗损(金损)
            $gold_loss = StringHelper::findNum($gold_loss);
            $lncl_loss_weight = $form->formatValue($goods['lncl_loss_weight'], 0) ?? 0;//含耗重
            $auto_loss_weight = ConfirmEnum::NO;
            if (bccomp($lncl_loss_weight, 0, 5) > 0) {
                $auto_loss_weight = ConfirmEnum::YES;
            }
            $gold_price = $form->formatValue($goods['gold_price'], 0) ?? 0;//金价
            $gold_amount = $form->formatValue($goods['gold_amount'], 0) ?? 0;//金料额
            $auto_gold_amount = ConfirmEnum::NO;
            if (bccomp($gold_amount, 0, 5) > 0) {
                $auto_gold_amount = ConfirmEnum::YES;
            }
            $pure_gold_rate = $form->formatValue($goods['pure_gold_rate'], 0) ?? 0;//折足率
//            if (!empty($peiliao_way)
//                && $peiliao_way == PeiLiaoWayEnum::LAILIAO
//                && empty($pure_gold_rate)) {
//                $flag = false;
//                $error[$i][] = "配料方式为来料加工，折足率必填";
//            }
            if (empty($peiliao_way) && $pure_gold_rate > 0) {
                $peiliao_way = PeiLiaoWayEnum::LAILIAO;
            }elseif(empty($peiliao_way) && !$pure_gold_rate && $suttle_weight){
                $peiliao_way = PeiLiaoWayEnum::FACTORY;
            }elseif(!$pure_gold_rate && !$suttle_weight){
                $peiliao_way = PeiLiaoWayEnum::NO_PEI;
            }else{

            }
            $main_pei_type = $form->formatValue($goods['main_pei_type'], 0) ?? 0;//主石配石方式
            $main_stone_sn = $goods['main_stone_sn'] ?? "";//主石编号
            $stone = $mainAttr = null;
            $cert_id = $cert_type = "";
            if (!empty($main_stone_sn)) {
                $stone = WarehouseStone::findOne(['stone_sn' => $main_stone_sn]);
                if (empty($stone)) {
//                    $flag = false;
//                    $error[$i][] = "主石编号：[" . $main_stone_sn . "]录入值有误";
                } else {
                    $cert_id = $stone->cert_id ?? "";
                    $cert_type = $stone->cert_type ?? "";

                    $mainAttr = $this->stoneAttrValueMap($stone, StonePositionEnum::MAIN_STONE);
                }
            }
            $main_stone_type = $goods['main_stone_type'] ?? "";//主石类型
            if (!empty($main_stone_type)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $main_stone_type, AttrIdEnum::MAIN_STONE_TYPE);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "主石类型：[" . $main_stone_type . "]录入值有误";
                    $main_stone_type = "";
                } else {
                    $main_stone_type = $attr_id;
                }
            } elseif (!empty($stone)) {
                $main_stone_type = $mainAttr['stone_type'] ?? "";
            }
            $main_stone_num = $form->formatValue($goods['main_stone_num'], 0) ?? 0;//主石粒数
            $main_stone_weight = $form->formatValue($goods['main_stone_weight'], 0) ?? 0;//主石重
            if (!empty($main_pei_type)) {
                $main_pei_type = \addons\Warehouse\common\enums\PeiShiWayEnum::getIdByName($main_pei_type);
                if (empty($main_pei_type) && $main_pei_type === "") {
                    $flag = false;
                    $error[$i][] = "主石配石方式：录入值有误";
                    $main_pei_type = 0;
                }
            } else {
                $main_pei_type = $form->getPeiType($main_stone_sn, $main_stone_num, $main_stone_weight);
            }
            $main_stone_price = $form->formatValue($goods['main_stone_price'], 0) ?? 0;//主石单价
            $main_stone_amount = $form->formatValue($goods['main_stone_amount'], 0) ?? 0;//主石成本
            $auto_main_stone = ConfirmEnum::NO;
            if (bccomp($main_stone_amount, 0, 5) > 0) {
                $auto_main_stone = ConfirmEnum::YES;
            }
            if (empty($main_stone_price) && !empty($stone)) {
                $main_stone_price = $stone->stone_price ?? 0;
            }
            $main_stone_shape = $goods['main_stone_shape'] ?? "";//主石形状
            if (!empty($main_stone_shape)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $main_stone_shape, AttrIdEnum::MAIN_STONE_SHAPE);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "主石形状：[" . $main_stone_shape . "]录入值有误";
                    $main_stone_shape = "";
                } else {
                    $main_stone_shape = $attr_id;
                }
            } elseif (!empty($stone)) {
                $main_stone_shape = $mainAttr['stone_shape'] ?? "";
            }
            $main_stone_color = $goods['main_stone_color'] ?? "";//主石颜色
            if (!empty($main_stone_color)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $main_stone_color, AttrIdEnum::MAIN_STONE_COLOR);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "主石颜色：[" . $main_stone_color . "]录入值有误";
                    $main_stone_color = "";
                } else {
                    $main_stone_color = $attr_id;
                }
            } elseif (!empty($stone)) {
                $main_stone_color = $mainAttr['stone_color'] ?? "";
            }
            $main_stone_clarity = $goods['main_stone_clarity'] ?? "";//主石净度
            if (!empty($main_stone_clarity)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $main_stone_clarity, AttrIdEnum::MAIN_STONE_CLARITY);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "主石净度：[" . $main_stone_clarity . "]录入值有误";
                    $main_stone_clarity = "";
                } else {
                    $main_stone_clarity = $attr_id;
                }
            } elseif (!empty($stone)) {
                $main_stone_clarity = $mainAttr['stone_clarity'] ?? "";
            }
            $main_stone_cut = $goods['main_stone_cut'] ?? "";//主石切工
            if (!empty($main_stone_cut)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $main_stone_cut, AttrIdEnum::MAIN_STONE_CUT);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "主石切工：[" . $main_stone_cut . "]录入值有误";
                    $main_stone_cut = "";
                } else {
                    $main_stone_cut = $attr_id;
                }
            } elseif (!empty($stone)) {
                $main_stone_cut = $mainAttr['stone_cut'] ?? "";
            }
            $main_stone_polish = $goods['main_stone_polish'] ?? "";//主石抛光
            if (!empty($main_stone_polish)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $main_stone_polish, AttrIdEnum::MAIN_STONE_POLISH);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "主石抛光：[" . $main_stone_polish . "]录入值有误";
                    $main_stone_polish = "";
                } else {
                    $main_stone_polish = $attr_id;
                }
            } elseif (!empty($stone)) {
                $main_stone_polish = $mainAttr['stone_polish'] ?? "";
            }
            $main_stone_symmetry = $goods['main_stone_symmetry'] ?? "";//主石对称
            if (!empty($main_stone_symmetry)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $main_stone_symmetry, AttrIdEnum::MAIN_STONE_SYMMETRY);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "主石对称：[" . $main_stone_symmetry . "]录入值有误";
                    $main_stone_symmetry = "";
                } else {
                    $main_stone_symmetry = $attr_id;
                }
            } elseif (!empty($stone)) {
                $main_stone_symmetry = $mainAttr['stone_symmetry'] ?? "";
            }
            $main_stone_fluorescence = $goods['main_stone_fluorescence'] ?? "";//主石荧光
            if (!empty($main_stone_fluorescence)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $main_stone_fluorescence, AttrIdEnum::MAIN_STONE_FLUORESCENCE);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "主石荧光：[" . $main_stone_fluorescence . "]录入值有误";
                    $main_stone_fluorescence = "";
                } else {
                    $main_stone_fluorescence = $attr_id;
                }
            } elseif (!empty($stone)) {
                $main_stone_fluorescence = $mainAttr['stone_fluorescence'] ?? "";
            }
            $main_stone_colour = $goods['main_stone_colour'] ?? "";//主石色彩
            if (!empty($main_stone_colour)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $main_stone_colour, AttrIdEnum::MAIN_STONE_COLOUR);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "主石色彩：[" . $main_stone_colour . "]录入值有误";
                    $main_stone_colour = "";
                } else {
                    $main_stone_colour = $attr_id;
                }
            } elseif (!empty($stone)) {
                $main_stone_colour = $mainAttr['stone_colour'] ?? "";
            }
            //公司配或工厂配，且颜色，净度未填，且石头类型为：钻石，则默认：颜色：H，净度：SI，填写了以填写为准
            if($main_pei_type != PeiShiWayEnum::NO_PEI
                && $main_stone_type == 169){//主石类型=钻石
                if(empty($main_stone_color)){
                    $main_stone_color = '51';//主石颜色=H
                }
                if(empty($main_stone_clarity)){
                    $main_stone_clarity = '448';//主石净度=SI
                }
            }
//            $main_stone_size = $goods[31] ?? "";
//            if (empty($main_stone_size)) {
//                $main_stone_size = $stone->stone_size ?? "";
//            }
            $second_pei_type = $form->formatValue($goods['second_pei_type'], 0) ?? 0;//副石1配石方式
            $second_stone_sn1 = $goods['second_stone_sn1'] ?? "";//副石1编号
            $stone = $second1Attr = null;
            if (!empty($second_stone_sn1)) {
                $stone = WarehouseStone::findOne(['stone_sn' => $second_stone_sn1]);
                if (empty($stone)) {
//                    $flag = false;
//                    $error[$i][] = "副石1编号：[" . $second_stone_sn1 . "]录入值有误";
                } else {
                    //var_dump($stone);die;
                    $second1Attr = $this->stoneAttrValueMap($stone, StonePositionEnum::SECOND_STONE1);
                }
            }
            $second_stone_type1 = $goods['second_stone_type1'] ?? "";//副石1类型
            if (!empty($second_stone_type1)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $second_stone_type1, AttrIdEnum::SIDE_STONE1_TYPE);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "副石1类型：[" . $second_stone_type1 . "]录入值有误";
                    $second_stone_type1 = "";
                } else {
                    $second_stone_type1 = $attr_id;
                }
            } elseif (!empty($stone)) {
                $second_stone_type1 = $second1Attr['stone_type'] ?? "";
            }
            $second_stone_num1 = $form->formatValue($goods['second_stone_num1'], 0) ?? 0;//副石1粒数
            $second_stone_weight1 = $form->formatValue($goods['second_stone_weight1'], 0) ?? 0;//副石1重
            if (!empty($second_pei_type)) {
                $second_pei_type = \addons\Warehouse\common\enums\PeiShiWayEnum::getIdByName($second_pei_type);
                if (empty($second_pei_type) && $second_pei_type === "") {
                    $flag = false;
                    $error[$i][] = "副石1配石方式：录入值有误";
                    $second_pei_type = 0;
                }
            } else {
                $second_pei_type = $form->getPeiType($second_stone_sn1, $second_stone_num1, $second_stone_weight1);
            }
            $second_stone_price1 = $form->formatValue($goods['second_stone_price1'], 0) ?? 0;//副石1单价
            $second_stone_amount1 = $form->formatValue($goods['second_stone_amount1'], 0) ?? 0;//副石1成本
            $auto_second_stone1 = ConfirmEnum::NO;
            if (bccomp($second_stone_amount1, 0, 5) > 0) {
                $auto_second_stone1 = ConfirmEnum::YES;
            }
            if (empty($second_stone_price1) && !empty($stone)) {
                $second_stone_price1 = $stone->stone_price ?? 0;
            }
            $second_stone_shape1 = $goods['second_stone_shape1'] ?? "";//副石1形状
            if (!empty($second_stone_shape1)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $second_stone_shape1, AttrIdEnum::SIDE_STONE1_SHAPE);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "副石1形状：[" . $second_stone_shape1 . "]录入值有误";
                    $second_stone_shape1 = "";
                } else {
                    $second_stone_shape1 = $attr_id;
                }
            } elseif (!empty($stone)) {
                $second_stone_shape1 = $second1Attr['stone_shape'] ?? "";
            }
            $second_stone_color1 = $goods['second_stone_color1'] ?? "";//副石1颜色
            if (!empty($second_stone_color1)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $second_stone_color1, AttrIdEnum::SIDE_STONE1_COLOR);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "副石1颜色：[" . $second_stone_color1 . "]录入值有误";
                    $second_stone_color1 = "";
                } else {
                    $second_stone_color1 = $attr_id;
                }
            } elseif (!empty($stone)) {
                $second_stone_color1 = $second1Attr['stone_color'] ?? "";
            }
            $second_stone_clarity1 = $goods['second_stone_clarity1'] ?? "";//副石1净度
            if (!empty($second_stone_clarity1)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $second_stone_clarity1, AttrIdEnum::SIDE_STONE1_CLARITY);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "副石1净度：[" . $second_stone_clarity1 . "]录入值有误";
                    $second_stone_clarity1 = "";
                } else {
                    $second_stone_clarity1 = $attr_id;
                }
            } elseif (!empty($stone)) {
                $second_stone_clarity1 = $second1Attr['stone_clarity'] ?? "";
            }
            $second_stone_cut1 = $goods['second_stone_cut1'] ?? "";//副石1切工
            if (!empty($second_stone_cut1)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $second_stone_cut1, AttrIdEnum::SIDE_STONE1_CUT);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "副石1切工：[" . $second_stone_cut1 . "]录入值有误";
                    $second_stone_cut1 = "";
                } else {
                    $second_stone_cut1 = $attr_id;
                }
            } elseif (!empty($stone)) {
                $second_stone_cut1 = $second1Attr['stone_cut'] ?? "";
            }
            $second_stone_colour1 = $goods['second_stone_colour1'] ?? "";//副石1色彩
            if (!empty($second_stone_colour1)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $second_stone_colour1, AttrIdEnum::SIDE_STONE1_COLOUR);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "副石1色彩：[" . $second_stone_colour1 . "]录入值有误";
                    $second_stone_colour1 = "";
                } else {
                    $second_stone_colour1 = $attr_id;
                }
            } elseif (!empty($stone)) {
                $second_stone_colour1 = $second1Attr['stone_colour'] ?? "";
            }
            //公司配或工厂配，且颜色，净度未填，且石头类型为：钻石，则默认：颜色：H，净度：SI，填写了以填写为准
            if($second_pei_type != PeiShiWayEnum::NO_PEI
                && $second_stone_type1 == 211){//副石1类型=钻石
                if(empty($second_stone_color1)){
                    $second_stone_color1 = '135';//副石1颜色=H
                }
                if(empty($second_stone_clarity1)){
                    $second_stone_clarity1 = '604';//副石1净度=SI
                }
            }
            $second_pei_type2 = $form->formatValue($goods['second_pei_type2'], 0) ?? 0;//副石2配石方式
            $second_stone_sn2 = $goods['second_stone_sn2'] ?? "";//副石2编号
            $stone = $second2Attr = null;
            if (!empty($second_stone_sn2)) {
                $stone = WarehouseStone::findOne(['stone_sn' => $second_stone_sn2]);
                if (empty($stone)) {
//                    $flag = false;
//                    $error[$i][] = "副石2编号：[" . $second_stone_sn2 . "]录入值有误";
                } else {
                    $second2Attr = $this->stoneAttrValueMap($stone, StonePositionEnum::SECOND_STONE2);
                }
            }
            $second_stone_type2 = $goods['second_stone_type2'] ?? "";//副石2类型
            if (!empty($second_stone_type2)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $second_stone_type2, AttrIdEnum::SIDE_STONE2_TYPE);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "副石2类型：[" . $second_stone_type2 . "]录入值有误";
                    $second_stone_type2 = "";
                } else {
                    $second_stone_type2 = $attr_id;
                }
            } elseif (!empty($stone)) {
                $second_stone_type2 = $second2Attr['stone_type'] ?? "";
            }
            $second_stone_num2 = $form->formatValue($goods['second_stone_num2'], 0) ?? 0;//副石2粒数
            $second_stone_weight2 = $form->formatValue($goods['second_stone_weight2'], 0) ?? 0;//副石2重
            if (!empty($second_pei_type2)) {
                $second_pei_type2 = \addons\Warehouse\common\enums\PeiShiWayEnum::getIdByName($second_pei_type2);
                if (empty($second_pei_type2) && $second_pei_type2 === "") {
                    $flag = false;
                    $error[$i][] = "副石2配石方式：录入值有误";
                    $second_pei_type2 = 0;
                }
            } else {
                $second_pei_type2 = $form->getPeiType($second_stone_sn2, $second_stone_num2, $second_stone_weight2);
            }
            $second_stone_price2 = $form->formatValue($goods['second_stone_price2'], 0) ?? 0;//副石2单价
            $second_stone_amount2 = $form->formatValue($goods['second_stone_amount2'], 0) ?? 0;//副石2成本
            $auto_second_stone2 = ConfirmEnum::NO;
            if (bccomp($second_stone_amount2, 0, 5) > 0) {
                $auto_second_stone2 = ConfirmEnum::YES;
            }
            if (empty($second_stone_price2) && !empty($stone)) {
                $second_stone_price2 = $stone->stone_price ?? 0;
            }
            $second_stone_color2 = $goods['second_stone_color2'] ?? "";//副石2颜色
            if (!empty($second_stone_color2)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $second_stone_color2, AttrIdEnum::SIDE_STONE2_COLOR);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "副石2颜色：[" . $second_stone_color2 . "]录入值有误";
                    $second_stone_color2 = "";
                } else {
                    $second_stone_color2 = $attr_id;
                }
            } elseif (!empty($stone)) {
                $second_stone_color2 = $second2Attr['stone_color'] ?? "";
            }
            $second_stone_clarity2 = $goods['second_stone_clarity2'] ?? "";//副石2净度
            if (!empty($second_stone_clarity2)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $second_stone_clarity2, AttrIdEnum::SIDE_STONE2_CLARITY);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "副石2净度：[" . $second_stone_clarity2 . "]录入值有误";
                    $second_stone_clarity2 = "";
                } else {
                    $second_stone_clarity2 = $attr_id;
                }
            } elseif (!empty($stone)) {
                $second_stone_clarity2 = $second2Attr['stone_clarity'] ?? "";
            }
            //公司配或工厂配，且颜色，净度未填，且石头类型为：钻石，则默认：颜色：H，净度：SI，填写了以填写为准
            if($second_pei_type2 != PeiShiWayEnum::NO_PEI
                && $second_stone_type2 == 225){//副石2类型=钻石
                if(empty($second_stone_color2)){
                    $second_stone_color2 = '636';//副石2颜色=H
                }
                if(empty($second_stone_clarity2)){
                    $second_stone_clarity2 = '613';//副石2净度=SI
                }
            }
            $second_pei_type3 = $form->formatValue($goods['second_pei_type3'], 0) ?? 0;//副石3配石方式
            $second_stone_sn3 = $goods['second_stone_sn3'] ?? "";//副石3编号
            $stone = $second3Attr = null;
            if (!empty($second_stone_sn3)) {
                $stone = WarehouseStone::findOne(['stone_sn' => $second_stone_sn3]);
                if (empty($stone)) {
//                    $flag = false;
//                    $error[$i][] = "副石3编号：[" . $second_stone_sn3 . "]录入值有误";
                } else {
                    $second3Attr = $this->stoneAttrValueMap($stone, StonePositionEnum::SECOND_STONE3);
                }
            }
            $second_stone_type3 = $goods['second_stone_type3'] ?? "";//副石3类型
            if (!empty($second_stone_type3)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $second_stone_type3, AttrIdEnum::SIDE_STONE3_TYPE);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "副石3类型：[" . $second_stone_type3 . "]录入值有误";
                    $second_stone_type3 = "";
                } else {
                    $second_stone_type3 = $attr_id;
                }
            } elseif (!empty($stone)) {
                $second_stone_type3 = $second3Attr['stone_type'] ?? "";
            }
            $second_stone_num3 = $form->formatValue($goods['second_stone_num3'], 0) ?? 0;//副石3粒数
            $second_stone_weight3 = $form->formatValue($goods['second_stone_weight3'], 0) ?? 0;//副石3重
            if (!empty($second_pei_type3)) {
                $second_pei_type3 = \addons\Warehouse\common\enums\PeiShiWayEnum::getIdByName($second_pei_type3);
                if (empty($second_pei_type3) && $second_pei_type3 === "") {
                    $flag = false;
                    $error[$i][] = "副石3配石方式：录入值有误";
                    $second_pei_type3 = 0;
                }
            } else {
                $second_pei_type3 = $form->getPeiType($second_stone_sn3, $second_stone_num3, $second_stone_weight3);
            }
            $second_stone_price3 = $form->formatValue($goods['second_stone_price3'], 0) ?? 0;//副石3单价
            $second_stone_amount3 = $form->formatValue($goods['second_stone_amount3'], 0) ?? 0;//副石3成本
            $auto_second_stone3 = ConfirmEnum::NO;
            if (bccomp($second_stone_amount3, 0, 5) > 0) {
                $auto_second_stone3 = ConfirmEnum::YES;
            }
            if (empty($second_stone_price3) && !empty($stone)) {
                $second_stone_price3 = $stone->stone_price ?? 0;
            }
            $second_stone_color3 = $goods['second_stone_color3'] ?? "";//副石3颜色
            if (!empty($second_stone_color3)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $second_stone_color3, AttrIdEnum::SIDE_STONE3_COLOR);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "副石3颜色：[" . $second_stone_color3 . "]录入值有误";
                    $second_stone_color3 = "";
                } else {
                    $second_stone_color3 = $attr_id;
                }
            } elseif (!empty($stone)) {
                $second_stone_color3 = $second3Attr['stone_color'] ?? "";
            }
            $second_stone_clarity3 = $goods['second_stone_clarity3'] ?? "";//副石3净度
            if (!empty($second_stone_clarity3)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $second_stone_clarity3, AttrIdEnum::SIDE_STONE3_CLARITY);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "副石3净度：[" . $second_stone_clarity3 . "]录入值有误";
                    $second_stone_clarity3 = "";
                } else {
                    $second_stone_clarity3 = $attr_id;
                }
            } elseif (!empty($stone)) {
                $second_stone_clarity3 = $second3Attr['stone_clarity'] ?? "";
            }
            //公司配或工厂配，且颜色，净度未填，且石头类型为：钻石，则默认：颜色：H，净度：SI，填写了以填写为准
            if($second_pei_type3 != PeiShiWayEnum::NO_PEI
                && $second_stone_type3 == 480){//副石3类型=钻石
                if(empty($second_stone_color3)){
                    $second_stone_color3 = '649';//副石3颜色=H
                }
                if(empty($second_stone_clarity3)){
                    $second_stone_clarity3 = '625';//副石3净度=SI
                }
            }
            $stone_remark = $goods['stone_remark'] ?? "";

            $parts_way = $form->formatValue($goods['parts_way'], 0) ?? "";//配件方式
            if (!empty($parts_way)) {
                $parts_way = \addons\Warehouse\common\enums\PeiJianWayEnum::getIdByName($parts_way);
                if (empty($parts_way) && $parts_way === "") {
                    $flag = false;
                    $error[$i][] = "配件方式：录入值有误";
                    $parts_way = 0;
                }
            }
            $parts_type = $goods['parts_type'] ?? "";//配件类型
            if (!empty($parts_type)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $parts_type, AttrIdEnum::MAT_PARTS_TYPE);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "配件类型：[" . $parts_type . "]录入值有误";
                    $parts_type = "";
                } else {
                    $parts_type = $attr_id ?? "";
                }
            }
            $parts_material = $goods['parts_material'] ?? "";//配件材质
            if (!empty($parts_material)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $parts_material, AttrIdEnum::MATERIAL_TYPE);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "配件材质：[" . $parts_material . "]录入值有误";
                    $parts_material = "";
                } else {
                    $parts_material = $attr_id;
                }
            }
            $parts_num = $form->formatValue($goods['parts_num'], 0) ?? 0;//配件数量
            $parts_gold_weight = $form->formatValue($goods['parts_gold_weight'], 0) ?? 0;//配件金重
            $parts_price = $form->formatValue($goods['parts_price'], 0) ?? 0;//配件金价
            $parts_amount = $form->formatValue($goods['parts_amount'], 0) ?? 0;//配件额
            $auto_parts_amount = ConfirmEnum::NO;
            if (bccomp($parts_amount, 0, 5) > 0) {
                $auto_parts_amount = ConfirmEnum::YES;
            }
            //$peishi_num = $form->formatValue($goods[57], 0) ?? 0;
            $peishi_weight = $form->formatValue($goods['peishi_weight'], 0) ?? 0;//配石重
            $peishi_gong_fee = $form->formatValue($goods['peishi_gong_fee'], 0) ?? 0;//配石工费
            $peishi_fee = $form->formatValue($goods['peishi_fee'], 0) ?? 0;//配石费
            $auto_peishi_fee = ConfirmEnum::NO;
            if (bccomp($peishi_fee, 0, 5) > 0) {
                $auto_peishi_fee = ConfirmEnum::YES;
            }
            $parts_fee = $form->formatValue($goods['parts_fee'], 0) ?? 0;//配件工费
            $xianqian_fee = $form->formatValue($goods['xianqian_fee'], 0) ?? 0;//镶石费
            $auto_xianqian_fee = ConfirmEnum::NO;
            if (bccomp($xianqian_fee, 0, 5) > 0) {
                $auto_xianqian_fee = ConfirmEnum::YES;
            }
            $gong_fee = $form->formatValue($goods['gong_fee'], 0) ?? 0;//克工费
            $piece_fee = $form->formatValue($goods['piece_fee'], 0) ?? 0;//件工费
//            if (!empty($gong_fee) && !empty($piece_fee)) {
//                $flag = false;
//                $error[$i][] = "[克/工费]和[件/工费]只能填其一";
//            }
            $xiangqian_craft = $goods['xiangqian_craft'] ?? "";//镶嵌工艺
            if (!empty($xiangqian_craft)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $xiangqian_craft, AttrIdEnum::XIANGQIAN_CRAFT);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "镶嵌工艺：[" . $xiangqian_craft . "]录入值有误";
                    $xiangqian_craft = "";
                } else {
                    $xiangqian_craft = $attr_id;
                }
            }
            $second_stone_fee1 = $form->formatValue($goods['second_stone_fee1'], 0) ?? 0;//配石1工费
            $second_stone_fee2 = $form->formatValue($goods['second_stone_fee2'], 0) ?? 0;//配石2工费
            $second_stone_fee3 = $form->formatValue($goods['second_stone_fee3'], 0) ?? 0;//配石3工费
            $biaomiangongyi = $goods['biaomiangongyi'] ?? "";//表面工艺
            if (!empty($biaomiangongyi)) {
                $biaomiangongyi = StringHelper::explode($biaomiangongyi, "|");
                $biaomiangongyi = array_unique(array_filter($biaomiangongyi));
                $attr_str = "";
                foreach ($biaomiangongyi as $item) {
                    $attr_id = $form->getAttrIdByAttrValue($style_sn, $item, AttrIdEnum::FACEWORK);
                    if (empty($attr_id)) {
                        $flag = false;
                        $error[$i][] = "表面工艺：[" . $item . "]录入值有误";
                        $biaomiangongyi = "";
                    } else {
                        $attr_str .= $attr_id . ",";
                    }
                }
                if (!empty($attr_str)) {
                    $biaomiangongyi = "," . $attr_str;
                }
            }
            $biaomiangongyi_fee = $form->formatValue($goods['biaomiangongyi_fee'], 0) ?? 0;//表面工艺费
            $fense_fee = $form->formatValue($goods['fense_fee'], 0) ?? 0;//分色费
            $penlasha_fee = $form->formatValue($goods['penlasha_fee'], 0) ?? 0;//喷砂费
            $lasha_fee = $form->formatValue($goods['lasha_fee'], 0) ?? 0;//拉砂费
            $bukou_fee = $form->formatValue($goods['bukou_fee'], 0) ?? 0;//补口费
            $templet_fee = $form->formatValue($goods['templet_fee'], 0) ?? 0;//版费
            $tax_fee = $form->formatValue($goods['tax_fee'], 0) ?? 0;//税费
            $tax_amount = $form->formatValue($goods['tax_amount'], 0) ?? 0;//税额
            $auto_tax_amount = ConfirmEnum::NO;
            if (bccomp($tax_amount, 0, 5) > 0) {
                $auto_tax_amount = ConfirmEnum::YES;
            }
            $cert_fee = $form->formatValue($goods['cert_fee'], 0) ?? 0;//证书费
            $other_fee = $form->formatValue($goods['other_fee'], 0) ?? 0;//其他费用
            $main_cert_id = $goods['main_cert_id'] ?? "";//主石证书号
            if (empty($main_cert_id)) {
                $main_cert_id = $cert_id;
            }
            $main_cert_type = $goods['main_cert_type'] ?? "";//主石证书类型
            if (!empty($main_cert_type)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $main_cert_type, AttrIdEnum::DIA_CERT_TYPE);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "主石证书类型：[" . $main_cert_type . "]录入值有误";
                    $main_cert_type = "";
                } else {
                    $main_cert_type = $attr_id;
                }
            } else {
                $main_cert_type = $cert_type;
            }
            $factory_cost = $form->formatValue($goods['factory_cost'], 0) ?? 0;//工厂总成本
            $auto_factory_cost = ConfirmEnum::NO;
            if (bccomp($factory_cost, 0, 5) > 0) {
                $auto_factory_cost = ConfirmEnum::YES;
            }
//            $cost_price = $form->formatValue($goods['cost_price'], 0) ?? 0;//公司成本价
//            $is_auto_price = ConfirmEnum::NO;
//            if (bccomp($cost_price, 0, 5) > 0) {
//                $is_auto_price = ConfirmEnum::YES;
//            }
            $cost_amount = $form->formatValue($goods['cost_amount'], 0) ?? 0;//公司成本总额
            $is_auto_price = ConfirmEnum::NO;
            $cost_price = 0;
            if (bccomp($cost_amount, 0, 5) > 0) {
                $is_auto_price = ConfirmEnum::YES;
                $cost_price = bcdiv(bcsub($cost_amount, $templet_fee, 3), $goods_num, 3);//单价成本价=(成本总额-版费)/数量
            }
            $markup_rate = $form->formatValue($goods['markup_rate'], 1) ?? 1;//倍率
            $remark = $goods['remark'] ?? "";//货品备注
            $saveData[] = $item = [
                'bill_id' => $bill->id,
                'bill_no' => $bill->bill_no,
                'bill_type' => $bill->bill_type,
                'goods_id' => $goods_id,
                'goods_sn' => $goods_sn,
                'style_id' => $style->id ?? $qiban->id,
                'style_sn' => $style_sn,
                'goods_image' => $style_image,
                'style_cate_id' => $style_cate_id,
                'product_type_id' => $product_type_id,
                'style_sex' => $style_sex,
                'style_channel_id' => $style_channel_id,
                'supplier_id' => $bill->supplier_id,
                'to_warehouse_id' => $to_warehouse_id,
                'put_in_type' => $bill->put_in_type,
                'qiban_sn' => $qiban_sn,
                'qiban_type' => $qiban_type,
                'goods_name' => $goods_name,
                'goods_num' => $goods_num,
                //属性信息
                'material_type' => $material_type,
                'material_color' => $material_color,
                'finger_hk' => $finger_hk,
                'finger' => $finger,
                'length' => $length,
                'product_size' => $product_size,
                'xiangkou' => $xiangkou,
                'kezi' => $kezi,
                'chain_type' => $chain_type,
                'cramp_ring' => $cramp_ring,
                'talon_head_type' => $talon_head_type,
                //金料信息
                'peiliao_way' => $peiliao_way,
                'suttle_weight' => $suttle_weight,
                //'gold_weight' => $gold_weight,
                'gold_loss' => $gold_loss,
                'lncl_loss_weight' => $lncl_loss_weight,
                'gold_price' => $gold_price,
                'gold_amount' => $gold_amount,
                'pure_gold_rate' => $pure_gold_rate,
                //主石信息
                'main_pei_type' => $main_pei_type,
                'main_stone_sn' => $main_stone_sn,
                'main_stone_type' => $main_stone_type,
                'main_stone_num' => $main_stone_num,
                'main_stone_weight' => $main_stone_weight,
                'main_stone_price' => $main_stone_price,
                'main_stone_amount' => $main_stone_amount,
                'main_stone_shape' => $main_stone_shape,
                'main_stone_color' => $main_stone_color,
                'main_stone_clarity' => $main_stone_clarity,
                'main_stone_polish' => $main_stone_polish,
                'main_stone_symmetry' => $main_stone_symmetry,
                'main_stone_fluorescence' => $main_stone_fluorescence,
                'main_stone_cut' => $main_stone_cut,
                'main_stone_colour' => $main_stone_colour,
//                'main_stone_size' => $main_stone_size,
                //副石1信息
                'second_pei_type' => $second_pei_type,
                'second_stone_sn1' => $second_stone_sn1,
                'second_stone_type1' => $second_stone_type1,
                'second_stone_num1' => $second_stone_num1,
                'second_stone_weight1' => $second_stone_weight1,
                'second_stone_price1' => $second_stone_price1,
                'second_stone_amount1' => $second_stone_amount1,
                'second_stone_shape1' => $second_stone_shape1,
                'second_stone_color1' => $second_stone_color1,
                'second_stone_clarity1' => $second_stone_clarity1,
                'second_stone_cut1' => $second_stone_cut1,
                'second_stone_colour1' => $second_stone_colour1,
                //副石2信息
                'second_pei_type2' => $second_pei_type2,
                'second_stone_sn2' => $second_stone_sn2,
                'second_stone_type2' => $second_stone_type2,
                'second_stone_num2' => $second_stone_num2,
                'second_stone_weight2' => $second_stone_weight2,
                'second_stone_price2' => $second_stone_price2,
                'second_stone_amount2' => $second_stone_amount2,
                'second_stone_color2' => $second_stone_color2,
                'second_stone_clarity2' => $second_stone_clarity2,
//                'second_stone_shape2' => $second_stone_shape2,
//                'second_stone_size2' => $second_stone_size2,
                //副石3信息
                'second_pei_type3' => $second_pei_type3,
                'second_stone_sn3' => $second_stone_sn3,
                'second_stone_type3' => $second_stone_type3,
                'second_stone_num3' => $second_stone_num3,
                'second_stone_weight3' => $second_stone_weight3,
                'second_stone_price3' => $second_stone_price3,
                'second_stone_amount3' => $second_stone_amount3,
                'second_stone_color3' => $second_stone_color3,
                'second_stone_clarity3' => $second_stone_clarity3,
                'stone_remark' => $stone_remark,
                //配件信息
                'parts_way' => $parts_way,
                'parts_type' => $parts_type,
                'parts_material' => $parts_material,
                'parts_num' => $parts_num,
                'parts_gold_weight' => $parts_gold_weight,
                'parts_price' => $parts_price,
//                'peishi_num' => $peishi_num,
                'parts_amount' => $parts_amount,
                //工费信息
                'peishi_weight' => $peishi_weight,
                'peishi_gong_fee' => $peishi_gong_fee,
                'peishi_fee' => $peishi_fee,
                'parts_fee' => $parts_fee,
                'gong_fee' => $gong_fee,
                'piece_fee' => $piece_fee,
                'xiangqian_craft' => $xiangqian_craft,
                //'xianqian_price' => $xianqian_price,
                'second_stone_fee1' => $second_stone_fee1,
                'second_stone_fee2' => $second_stone_fee2,
                'second_stone_fee3' => $second_stone_fee3,
                'xianqian_fee' => $xianqian_fee,
                'biaomiangongyi' => $biaomiangongyi,
                'biaomiangongyi_fee' => $biaomiangongyi_fee,
                'fense_fee' => $fense_fee,
                'penlasha_fee' => $penlasha_fee,
                'lasha_fee' => $lasha_fee,
                'bukou_fee' => $bukou_fee,
                'templet_fee' => $templet_fee,
                'cert_fee' => $cert_fee,
                'tax_fee' => $tax_fee,
                'other_fee' => $other_fee,
                'main_cert_id' => $main_cert_id,
                'main_cert_type' => $main_cert_type,
                //价格信息
                'tax_amount' => $tax_amount,
                'factory_cost' => $factory_cost,
                'cost_price' => $cost_price,
                'cost_amount' => $cost_amount,
                //其他信息
                'is_wholesale' => $is_wholesale,
                'is_auto_price' => $is_auto_price,
                'auto_loss_weight' => $auto_loss_weight,
                'auto_gold_amount' => $auto_gold_amount,
                'auto_main_stone' => $auto_main_stone,
                'auto_second_stone1' => $auto_second_stone1,
                'auto_second_stone2' => $auto_second_stone2,
                'auto_second_stone3' => $auto_second_stone3,
                'auto_parts_amount' => $auto_parts_amount,
                'auto_peishi_fee' => $auto_peishi_fee,
                'auto_xianqian_fee' => $auto_xianqian_fee,
                'auto_factory_cost' => $auto_factory_cost,
                'auto_tax_amount' => $auto_tax_amount,
                'markup_rate' => $markup_rate,
                'jintuo_type' => $jintuo_type,
                'is_inlay' => $is_inlay,
                'auto_goods_id' => $auto_goods_id,
                'remark' => $remark,
                'status' => StatusEnum::ENABLED,
                'creator_id' => \Yii::$app->user->identity->getId(),
                'created_at' => time(),
            ];
            $goodsM = new WarehouseBillGoodsL();
            $goodsM->setAttributes($item);
            if (!$goodsM->validate()) {
                $flag = false;
                $error[$i][] = $this->getError($goodsM);
            }else{
                $result = $form->updateFromValidate($goodsM);
                if ($result['error'] == false) {
                    $flag = false;
                    $error[$i][] = $result['msg'];
                }
            }
            if (!$flag && !empty($style_sn)) {
                //$error[$i] = array_unshift($error[$i], "[" . $style_sn . "]");
            }
            $i++;
        }
        if (!$flag) {
            //发生错误
            $message = "*注：填写属性值有误可能为以下情况：①填写格式有误 ②该款式属性下无此属性值<hr><hr>";
            foreach ($error as $k => $v) {
                $line = $k + 1;
                $style_sn = "";
                if (isset($style_sns[$k]) && !empty($style_sns[$k])) {
                    $style_sn = $style_sns[$k] ?? "";
                }
                $s = "【" . implode('】,【', $v) . '】';
                $message .= '第' . $line . '行：款号' . $style_sn . $s . '<hr>';
            }
            if ($error_off && count($error) > 0 && $message) {
                header("Content-Disposition: attachment;filename=错误提示" . date('YmdHis') . ".log");
                echo iconv("utf-8", "gbk", str_replace("<hr>", "\r\n", $message));
                exit();
            }
            throw new \Exception($message);
        }
        if (empty($saveData)) {
            throw new \Exception("数据不能为空");
        } else {
            $saveData = array_reverse($saveData);//倒序
        }
//        echo '<pre>';
//        var_dump($saveData);die;
        $value = [];
        $key = array_keys($saveData[0]);
        foreach ($saveData as $item) {
            $goodsM = new WarehouseBillGoodsL();
            $goodsM->setAttributes($item);
            if (!$goodsM->validate()) {
                throw new \Exception($this->getError($goodsM));
            }
            $value[] = array_values($item);
            if (count($value) >= 10) {
                $res = Yii::$app->db->createCommand()->batchInsert(WarehouseBillGoodsL::tableName(), $key, $value)->execute();
                if (false === $res) {
                    throw new \Exception("创建收货单据明细失败1");
                }
                $value = [];
            }
        }
        if (!empty($value)) {
            $res = \Yii::$app->db->createCommand()->batchInsert(WarehouseBillGoodsL::tableName(), $key, $value)->execute();
            if (false === $res) {
                throw new \Exception("创建收货单据明细失败2");
            }
        }

        //同步更新价格
        $this->syncUpdatePriceAll($bill);

        //同步更新单头信息
        $this->warehouseBillTSummary($form->bill_id);
    }

    /**
     *
     * 批量编辑
     * @param $ids
     * @param WarehouseBillTGoodsForm $form
     * @return object
     * @throws
     */
    public function batchEdit($form)
    {
        $id_arr = array_unique($form->getIds());
        $name = $form->batch_name;
        $value = $form->batch_value;
        $updateIds = [];
        foreach ($id_arr as $id) {
            $goods = WarehouseBillTGoodsForm::findOne(['id' => $id]);
            $goods->$name = $value;
            if (false === $goods->validate()) {
                throw new \Exception($this->getError($goods));
            }
            $result = $form->updateFromValidate($goods);
            if ($result['error'] == false) {
                throw new \Exception($result['msg']);
            }
            if($goods->style_sn){
                $form->getAttrValueListByStyle($goods->style_sn, 1);
                $updateIds[] = $id;
            }else{
                $updateIds[] = $id;
            }
            $form->bill_id = $goods->bill_id;
        }
        if($updateIds){
            $res = WarehouseBillTGoodsForm::updateAll([$name => $value], $updateIds);
            if ($res == false) {
                throw new \Exception("批量填充失败");
            }
            $this->syncUpdatePriceAll(null, $updateIds);
            $this->WarehouseBillTSummary($form->bill_id);
        }
        return $form;
    }

    /**
     *
     * 同步更新单据商品价格
     * @param $ids
     * @param WarehouseBillTForm $form
     * @return object
     * @throws
     */
    public function syncUpdatePriceAll($form = null, $ids = [])
    {
        $where = [];
        if (!empty($ids)) {
            $where = array_merge($where, ['id' => $ids]);
        }
        if($form){
            $where = array_merge($where, ['bill_id' => $form->id]);
        }
        if(!empty($where)){
            $goods = WarehouseBillTGoodsForm::findAll($where);
            if (!empty($goods)) {
                foreach ($goods as $good) {
                    $this->syncUpdatePrice($good);
                }
            }
        }
        return $goods ?? null;
    }

    /**
     *
     * 金重=(连石重-(主石重*数量)-副石1重-副石2重-副石3重-(配件重*数量))
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateGoldWeight($form)
    {
        $stone_weight = bcadd($this->calculateMainStoneWeight($form), $this->calculateSecondStoneWeight($form), 5);
        $stone_weight = bcmul($stone_weight, 0.2, 5);//ct转换为克重
        $weight = bcsub($form->suttle_weight, $stone_weight, 5) ?? 0;
        $weight = bcsub($weight, $this->calculatePartsWeight($form), 5) ?? 0;
        //var_dump('主石重:'.$this->calculateMainStoneWeight($form),'副石总重'.$this->calculateSecondStoneWeight($form),'主+副:'.($stone_weight/0.2),'转克重:'.$stone_weight,'配件重:'.$this->calculatePartsWeight($form),'实重:'.$weight);die;
        if (bccomp(0, $weight, 5) == 1) {
            $weight = 0;
        }
        return $weight ?? 0;
    }

    /**
     *
     * 含耗重(g)=(金重(g)*(1+损耗(%)))
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateLossWeight($form)
    {
        if ($form->auto_loss_weight) {
            return $form->lncl_loss_weight ?? 0;
        }
        if (empty($form->gold_loss)) {
            $form->gold_loss = 0;
        }
        return bcmul($this->calculateGoldWeight($form), 1 + ($form->gold_loss / 100), 5) ?? 0;
    }

    /**
     *
     * 折足(g)=(含耗重*折足率(%))
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculatePureGold($form)
    {
        if ($form->peiliao_way == PeiLiaoWayEnum::LAILIAO) {
            if (bccomp($form->pure_gold_rate, 0, 5) != 1) {
                $form->pure_gold_rate = 0;
            }
            return bcmul($this->calculateLossWeight($form), ($form->pure_gold_rate / 100), 5) ?? 0;
        }
        return 0;
    }

    /**
     *
     * 金料额=(金价*金重*(1+损耗))
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateGoldAmount($form)
    {
        if ($form->auto_gold_amount) {
            return $form->gold_amount ?? 0;
        }
        return bcmul($form->gold_price, $this->calculateLossWeight($form), 5) ?? 0;

    }

    /**
     *
     * 主石总重(ct)=(主石单颗重(ct)*主石数量)作废
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateMainStoneWeight($form)
    {
        if ($form->main_pei_type == PeiShiWayEnum::NO_PEI) {
            return 0;
        }
        //return bcmul($form->main_stone_weight, $form->main_stone_num, 5) ?? 0;
        return $form->main_stone_weight ?? 0;
    }

    /**
     *
     * 副石总数量=(副石1数量+副石2数量+副石3数量)
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateSecondStoneNum($form)
    {
        return bcadd(bcadd($form->second_stone_num1, $form->second_stone_num2, 5), $form->second_stone_num3, 5) ?? 0;
    }

    /**
     *
     * 副石总重(ct)=(副石1重(ct)+副石2重(ct)+副石3重(ct))
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateSecondStoneWeight($form)
    {
        $second_stone_weight1 = $form->second_stone_weight1 ?? 0;
        $second_stone_weight2 = $form->second_stone_weight2 ?? 0;
        $second_stone_weight3 = $form->second_stone_weight3 ?? 0;
        if ($form->second_pei_type == PeiShiWayEnum::NO_PEI) {
            $second_stone_weight1 = 0;
        }
        if ($form->second_pei_type2 == PeiShiWayEnum::NO_PEI) {
            $second_stone_weight2 = 0;
        }
        if ($form->second_pei_type3 == PeiShiWayEnum::NO_PEI) {
            $second_stone_weight3 = 0;
        }
        return bcadd(bcadd($second_stone_weight1, $second_stone_weight2, 5), $second_stone_weight3, 5) ?? 0;
    }

    /**
     *
     * 主石成本=(主石重*单价)
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateMainStoneCost($form)
    {
        if ($form->auto_main_stone) {
            return $form->main_stone_amount ?? 0;
        }
        return bcmul($this->calculateMainStoneWeight($form), $form->main_stone_price, 5) ?? 0;
    }

    /**
     *
     * 副石1成本=(副石1重*单价)
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateSecondStone1Cost($form)
    {
        if ($form->auto_second_stone1) {
            return $form->second_stone_amount1 ?? 0;
        }
        return bcmul($form->second_stone_weight1, $form->second_stone_price1, 5) ?? 0;
    }

    /**
     *
     * 副石2成本=(副石2重*单价)
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateSecondStone2Cost($form)
    {
        if ($form->auto_second_stone2) {
            return $form->second_stone_amount2 ?? 0;
        }
        return bcmul($form->second_stone_weight2, $form->second_stone_price2, 5) ?? 0;
    }

    /**
     *
     * 副石3成本=(副石3重*单价)
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateSecondStone3Cost($form)
    {
        if ($form->auto_second_stone3) {
            return $form->second_stone_amount3 ?? 0;
        }
        return bcmul($form->second_stone_weight3, $form->second_stone_price3, 5) ?? 0;
    }

    /**
     *
     * 配件总重(g)=(配件重(g)*配件数量)
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculatePartsWeight($form)
    {
        if ($form->parts_way == PeiJianWayEnum::NO_PEI) {
            return 0;
        }
        return bcmul($form->parts_gold_weight, 1, 5) ?? 0;//$form->parts_num
    }

    /**
     *
     * 配件额=(配件总重(g)*配件金价/g)
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculatePartsAmount($form)
    {
        if ($form->auto_parts_amount) {
            return $form->parts_amount ?? 0;
        }
        return bcmul($this->calculatePartsWeight($form), $form->parts_price, 5) ?? 0;
    }

    /**
     *
     * 配石费=((副石重/数量)小于0.03ct的，*数量*配石工费)[配石费=(配石重量*配石工费/ct)]
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculatePeishiFee($form)
    {
        if ($form->auto_peishi_fee) {
            return $form->peishi_fee ?? 0;
        }
        if (bccomp($form->peishi_weight, 0, 5) != 1) {
            $form->peishi_weight = $this->calculateSecondStoneWeight($form);
        }
        return bcmul($form->peishi_weight, $form->peishi_gong_fee, 5) ?? 0;
    }

    /**
     *
     * 税额=(金重*税费)
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateTaxAmount($form)
    {
        if ($form->auto_tax_amount) {
            return $form->tax_amount ?? 0;
        }
        return bcmul($this->calculateGoldWeight($form), $form->tax_fee, 5) ?? 0;
    }

    /**
     *
     * 基本工费=(克/工费*含耗重)
     * ps:填了件工费，基本工费=件工费
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateBasicGongFee($form)
    {
        if (bccomp($form->piece_fee, 0, 5) > 0) {
            return $form->piece_fee ?? 0;
        }
        return bcmul($form->gong_fee, $this->calculateLossWeight($form), 5) ?? 0;
    }

    /**
     *
     * 镶石费=镶石1费+镶石2费+镶石3费
     * 【镶石1费=镶石1单价/颗*副石1数量；镶石2费=镶石2单价/颗*副石2数量；镶石3费=镶石3单价/颗*副石3数量；】
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateXiangshiFee($form)
    {
        if ($form->auto_xianqian_fee) {
            return $form->xianqian_fee ?? 0;
        }
        $second_stone_fee1 = bcmul($form->second_stone_fee1, $form->second_stone_num1, 5) ?? 0;
        $second_stone_fee2 = bcmul($form->second_stone_fee2, $form->second_stone_num2, 5) ?? 0;
        $second_stone_fee3 = bcmul($form->second_stone_fee3, $form->second_stone_num3, 5) ?? 0;
        return bcadd($second_stone_fee1, bcadd($second_stone_fee2, $second_stone_fee3, 5), 5) ?? 0;
    }

    /**
     *
     * 总工费=(配石费+基本工费+配件工费+镶石费+表面工艺费+分色分件费+喷砂费+拉砂费+补口费+版费+税费+证书费+其它费用)
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateTotalGongFee($form)
    {
        $total_gong_fee = 0;
        $total_gong_fee = bcadd($total_gong_fee, $this->calculatePeishiFee($form), 5);
        //$total_gong_fee = bcadd($total_gong_fee, $form->piece_fee, 5);//件/工费
        $total_gong_fee = bcadd($total_gong_fee, $this->calculateBasicGongFee($form), 5);
        $total_gong_fee = bcadd($total_gong_fee, $form->parts_fee, 5);
        $total_gong_fee = bcadd($total_gong_fee, $this->calculateXiangshiFee($form), 5);
        $total_gong_fee = bcadd($total_gong_fee, $form->biaomiangongyi_fee, 5);//表面工艺费
        $total_gong_fee = bcadd($total_gong_fee, $form->fense_fee, 5);//分件/分色费
        $total_gong_fee = bcadd($total_gong_fee, $form->penlasha_fee, 5);//喷砂费
        $total_gong_fee = bcadd($total_gong_fee, $form->lasha_fee, 5);//拉砂费
        $total_gong_fee = bcadd($total_gong_fee, $form->bukou_fee, 5);//补口费
        //$total_gong_fee = bcadd($total_gong_fee, $form->extra_stone_fee, 5);//超石费
        $total_gong_fee = bcadd($total_gong_fee, $form->templet_fee, 5);//样板工费
        $total_gong_fee = bcadd($total_gong_fee, $form->tax_amount, 5);//税费
        $total_gong_fee = bcadd($total_gong_fee, $form->cert_fee, 5);//证书费
        $total_gong_fee = bcadd($total_gong_fee, $form->other_fee, 5);//其它补充费用

        return sprintf("%.3f", $total_gong_fee) ?? 0;
    }

    /**
     *
     * 工厂成本=(配料(厂配)+主副石成本(厂配)+配件(厂配)+总工费)
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateFactoryCost($form)
    {
        if ($form->auto_factory_cost) {
            return $form->factory_cost ?? 0;
        }
        $factory_cost = 0;
        if ($form->peiliao_way == PeiLiaoWayEnum::FACTORY) {
            $factory_cost = bcadd($factory_cost, $this->calculateGoldAmount($form), 5);
        }
        if ($form->main_pei_type == PeiShiWayEnum::FACTORY) {
            $factory_cost = bcadd($factory_cost, $this->calculateMainStoneCost($form), 5);
        }
        if ($form->second_pei_type == PeiShiWayEnum::FACTORY) {
            $factory_cost = bcadd($factory_cost, $this->calculateSecondStone1Cost($form), 5);
        }
        if ($form->second_pei_type2 == PeiShiWayEnum::FACTORY) {
            $factory_cost = bcadd($factory_cost, $this->calculateSecondStone2Cost($form), 5);
        }
        if ($form->second_pei_type3 == PeiShiWayEnum::FACTORY) {
            $factory_cost = bcadd($factory_cost, $this->calculateSecondStone3Cost($form), 5);
        }
        if ($form->parts_way == PeiJianWayEnum::FACTORY) {
            $factory_cost = bcadd($factory_cost, $this->calculatePartsAmount($form), 5);
        }
        $factory_cost = bcadd($factory_cost, $this->calculateTotalGongFee($form), 5);//总工费

        return sprintf("%.3f", $factory_cost) ?? 0;
    }

    /**
     *
     * 公司成本/单价(成本价/单价)=(金料额+主石成本+副石1成本+副石2成本+副石3成本+配件额+总工费-版费)/数量
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateCostPrice($form)
    {
        $cost_price = 0;
        if ($form->peiliao_way != PeiLiaoWayEnum::NO_PEI) {
            $cost_price = bcadd($cost_price, $this->calculateGoldAmount($form), 5);
        }
        if ($form->main_pei_type != PeiShiWayEnum::NO_PEI) {
            $cost_price = bcadd($cost_price, $this->calculateMainStoneCost($form), 5);
        }
        if ($form->second_pei_type != PeiShiWayEnum::NO_PEI) {
            $cost_price = bcadd($cost_price, $this->calculateSecondStone1Cost($form), 5);
        }
        if ($form->second_pei_type2 != PeiShiWayEnum::NO_PEI) {
            $cost_price = bcadd($cost_price, $this->calculateSecondStone2Cost($form), 5);
        }
        if ($form->second_pei_type3 != PeiShiWayEnum::NO_PEI) {
            $cost_price = bcadd($cost_price, $this->calculateSecondStone3Cost($form), 5);
        }
        if ($form->parts_way != PeiJianWayEnum::NO_PEI) {
            $cost_price = bcadd($cost_price, $this->calculatePartsAmount($form), 5);
        }
        $cost_price = bcadd($cost_price, $this->calculateTotalGongFee($form), 5);
        $cost_price = bcsub($cost_price, $form->templet_fee, 3);//版费
        $cost_price = bcdiv($cost_price, $form->goods_num, 5);
        return sprintf("%.3f", $cost_price) ?? 0;
    }

    /**
     *
     * 公司成本总额=(公司成本/件+版费/件)*商品数量
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateCostAmount($form)
    {
        //$templet_fee = bcdiv($form->templet_fee, $form->goods_num, 3) ?? 0;//单件版费
        $cost_price = bcmul($form->cost_price, $form->goods_num, 3) ?? 0;
        return bcadd($cost_price, $form->templet_fee, 3) ?? 0;//+版费
    }

    /**
     *
     * 标签价(市场价)=(单件成本*倍率)
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateMarketPrice($form)
    {
        return bcmul($form->markup_rate, $form->cost_price, 5) ?? 0;
    }

    /**
     *
     * 同步更新商品价格
     * @param WarehouseBillTGoodsForm $form
     * @return object
     * @throws
     */
    public function syncUpdatePrice($form)
    {
        if (!$form->validate()) {
            throw new \Exception($this->getError($form));
        }
        $form->gold_weight = $this->calculateGoldWeight($form);//金重
        if (empty($form->auto_loss_weight) || bccomp($form->lncl_loss_weight, 0, 5) != 1) {
            if (bccomp($form->lncl_loss_weight, 0, 5) != 1) {
                $form->auto_loss_weight = ConfirmEnum::NO;
            }
            $form->lncl_loss_weight = $this->calculateLossWeight($form);//含耗重
        }
        $form->pure_gold = $this->calculatePureGold($form);//折足
        if (empty($form->auto_gold_amount) || bccomp($form->gold_amount, 0, 5) != 1) {
            if (bccomp($form->gold_amount, 0, 5) != 1) {
                $form->auto_gold_amount = ConfirmEnum::NO;
            }
            $form->gold_amount = $this->calculateGoldAmount($form);//金料额
        }
        if (empty($form->auto_main_stone) || bccomp($form->main_stone_amount, 0, 5) != 1) {
            if (bccomp($form->main_stone_amount, 0, 5) != 1) {
                $form->auto_main_stone = ConfirmEnum::NO;
            }
            $form->main_stone_amount = $this->calculateMainStoneCost($form);//主石成本
        }
        if (empty($form->auto_second_stone1) || bccomp($form->second_stone_amount1, 0, 5) != 1) {
            if (bccomp($form->second_stone_amount1, 0, 5) != 1) {
                $form->auto_second_stone1 = ConfirmEnum::NO;
            }
            $form->second_stone_amount1 = $this->calculateSecondStone1Cost($form);//副石1成本
        }
        if (empty($form->auto_second_stone2) || bccomp($form->second_stone_amount2, 0, 5) != 1) {
            if (bccomp($form->second_stone_amount2, 0, 5) != 1) {
                $form->auto_second_stone2 = ConfirmEnum::NO;
            }
            $form->second_stone_amount2 = $this->calculateSecondStone2Cost($form);//副石2成本
        }
        if (empty($form->auto_second_stone3) || bccomp($form->second_stone_amount3, 0, 5) != 1) {
            if (bccomp($form->second_stone_amount3, 0, 5) != 1) {
                $form->auto_second_stone3 = ConfirmEnum::NO;
            }
            $form->second_stone_amount3 = $this->calculateSecondStone3Cost($form);//副石3成本
        }
        if (empty($form->auto_peishi_fee) || bccomp($form->peishi_fee, 0, 5) != 1) {
            if (bccomp($form->peishi_fee, 0, 5) != 1) {
                $form->auto_peishi_fee = ConfirmEnum::NO;
            }
            $form->peishi_fee = $this->calculatePeishiFee($form);//配石费
        }
        if (empty($form->auto_xianqian_fee) || bccomp($form->xianqian_fee, 0, 5) != 1) {
            if (bccomp($form->xianqian_fee, 0, 5) != 1) {
                $form->auto_xianqian_fee = ConfirmEnum::NO;
            }
            $form->xianqian_fee = $this->calculateXiangshiFee($form);//镶石费
        }
        if (empty($form->auto_parts_amount) || bccomp($form->parts_amount, 0, 5) != 1) {
            if (bccomp($form->parts_amount, 0, 5) != 1) {
                $form->auto_parts_amount = ConfirmEnum::NO;
            }
            $form->parts_amount = $this->calculatePartsAmount($form);//配件额
        }
        $form->basic_gong_fee = $this->calculateBasicGongFee($form);//基本工费
        $form->total_gong_fee = $this->calculateTotalGongFee($form);//总工费
        if (empty($form->auto_factory_cost) || bccomp($form->factory_cost, 0, 5) != 1) {
            if (bccomp($form->factory_cost, 0, 5) != 1) {
                $form->auto_factory_cost = ConfirmEnum::NO;
            }
            $form->factory_cost = $this->calculateFactoryCost($form);//工厂成本
        }
        if (empty($form->auto_tax_amount) || bccomp($form->tax_amount, 0, 5) != 1) {
            if (bccomp($form->tax_amount, 0, 5) != 1) {
                $form->auto_tax_amount = ConfirmEnum::NO;
            }
            $form->tax_amount = $this->calculateTaxAmount($form);//税额
        }
        if (empty($form->is_auto_price) || bccomp($form->cost_price, 0, 5) != 1) {
            $form->cost_price = $this->calculateCostPrice($form);//公司成本/件
        }
        $form->cost_amount = $this->calculateCostAmount($form);//公司成本总额
        $form->market_price = $this->calculateMarketPrice($form);//标签价
        if (false === $form->save()) {
            throw new \Exception($this->getError($form));
        }

        return $form;
    }

    /**
     *
     * 石料属性值映射
     * @param WarehouseStone $stone
     * @param int $stone_position
     * @return array
     * @throws
     */
    public function stoneAttrValueMap($stone, $stone_position)
    {
        $type = $this->getStoneTypeMap();
        $shape = $this->getStoneShapeMap();
        $color = $this->getStoneColorMap();
        $clarity = $this->getStoneClarityMap();
        $cut = $this->getStoneCutMap();
        $colour = $this->getStoneColourMap();
        $stoneAttr = [];
        if (!empty($stone)) {
            $stone_type = $stone->stone_type ?? "";
            $stone_type = $type[$stone_type] ?? [];
            $stone_shape = $stone->stone_shape ?? "";
            $stone_shape = $shape[$stone_shape] ?? [];
            $stone_color = $stone->stone_color ?? "";
            $stone_color = $color[$stone_color] ?? [];
            $stone_clarity = $stone->stone_clarity ?? "";
            $stone_clarity = $clarity[$stone_clarity] ?? [];
            $stone_cut = $stone->stone_cut ?? "";
            $stone_cut = $cut[$stone_cut] ?? [];
            $stone_colour = $stone->stone_colour ?? "";
            $stone_colour = $colour[$stone_colour] ?? [];
            switch ($stone_position) {
                case StonePositionEnum::MAIN_STONE:
                    $stoneAttr['stone_type'] = $stone_type[0] ?? "";
                    $stoneAttr['stone_shape'] = $stone_shape[0] ?? "";
                    $stoneAttr['stone_color'] = $stone_color[0] ?? "";
                    $stoneAttr['stone_clarity'] = $stone_clarity[0] ?? "";
                    $stoneAttr['stone_cut'] = $stone_cut[0] ?? "";
                    $stoneAttr['stone_colour'] = $stone_colour[0] ?? "";
                    $stoneAttr['stone_polish'] = $stone->stone_polish ?? "";
                    $stoneAttr['stone_symmetry'] = $stone->stone_symmetry ?? "";
                    $stoneAttr['stone_fluorescence'] = $stone->stone_fluorescence ?? "";
                    break;
                case StonePositionEnum::SECOND_STONE1:
                    $stoneAttr['stone_type'] = $stone_type[1] ?? "";
                    $stoneAttr['stone_shape'] = $stone_shape[1] ?? "";
                    $stoneAttr['stone_color'] = $stone_color[0] ?? "";
                    $stoneAttr['stone_clarity'] = $stone_clarity[0] ?? "";
                    $stoneAttr['stone_cut'] = $stone_cut[0] ?? "";
                    $stoneAttr['stone_colour'] = $stone_colour[1] ?? "";
                    break;
                case StonePositionEnum::SECOND_STONE2:
                    $stoneAttr['stone_type'] = $stone_type[2] ?? "";
                    $stoneAttr['stone_shape'] = $stone_shape[1] ?? "";
                    $stoneAttr['stone_color'] = $stone_color[1] ?? "";
                    $stoneAttr['stone_clarity'] = $stone_clarity[1] ?? "";
                    break;
                case StonePositionEnum::SECOND_STONE3:
                    $stoneAttr['stone_type'] = $stone_type[3] ?? "";
                    $stoneAttr['stone_color'] = $stone_color[2] ?? "";
                    $stoneAttr['stone_clarity'] = $stone_clarity[2] ?? "";
                    break;
                default:
                    break;
            }
            if (!empty($stoneAttr)) {
                foreach ($stoneAttr as $k => &$item) {
                    if ($item) {
                        $stoneAttr[$k] = (string)$item;
                    }
                }
            }
        }
        return $stoneAttr ?? [];
    }

    /**
     * 石头类型
     * 主石 => [副石1, 副石2, 副石3]
     * @return array
     */
    public function getStoneTypeMap()
    {
        return [
            241 => [193, 217, 227, 482],//莫桑石
            234 => [169, 211, 225, 480],//钻石
            235 => [192, 216, 226, 481],//锆石
        ];
    }

    /**
     * 石头类型
     * 主石 => [副石1, 副石2, 副石3]
     * @return array
     */
    public function getStoneType1Map()
    {
        return [
            530 => [534, 538, 542],//拖帕石
            529 => [533, 537, 541],//黑陶瓷
            528 => [532, 536, 540],//葡萄石
            527 => [531, 535, 539],//蜜蜡
            506 => [214, 507, 517],//玉石
            497 => [505, 516, 526],//猫眼石
            496 => [504, 515, 525],//珊瑚
            495 => [503, 514, 524],//砗磲
            494 => [502, 513, 523],//青金石
            493 => [501, 512, 522],//立方氧化锆
            473 => [500, 511, 521],//绿松石
            472 => [499, 510, 520],//和田玉
            169 => [211, 225, 480],//钻石
            192 => [216, 226, 481],//锆石
            193 => [217, 227, 482],//莫桑石
            194 => [212, 228, 483],//红宝石
            426 => [432, 509, 519],//蓝宝石
            195 => [252, 229, 485],//翡翠
            196 => [213, 230, 484],//珍珠
            231 => [358, 232, 486],//贝母
            427 => [433, 357, 487],//晶石(水晶)
            428 => [434, 438, 488],//玉髓
            429 => [435, 439, 489],//碧玺
            430 => [436, 440, 490],//玛瑙
            431 => [437, 441, 491],//琥珀
            470 => [498, 508, 518],//石榴石
            424 => [215, 356, 492],//其它
        ];
    }


    /**
     * 形状
     * 主石 => [副石1, 副石2]
     * @return array
     */
    public function getStoneShapeMap()
    {
        return [
            16 => [361, 379],//圆形
            17 => [362, 380],//椭圆形
            54 => [363, 381],//公主方形
            55 => [364, 382],//八角梯形
            56 => [365, 383],//心形
            57 => ["", ""],//马眼形
            58 => [367, 385],//枕形
            452 => [453, 454],//祖母绿形
            59 => [368, 386],//水滴形
            450 => ["", ""],//阿斯切
            60 => [369, 387],//雷迪恩形
            61 => [370, 388],//圆三角形
            315 => [371, 389],//圆形(弧面)
            316 => [372, 390],//椭圆(蛋形-弧面)
            317 => [373, 391],//异形(弧面)
            318 => [374, 392],//球体(弧面)
            319 => [375, 393],//心形(弧面)
            320 => [376, 394],//不规则(弧面)
            455 => [458, 461],//三角形
            456 => [459, 462],//梯方形
            457 => [460, 463],//长方形
            377 => [378, 395],//其它
        ];
    }

    /**
     * 颜色
     * 主石 => (副石1, 副石2, 副石3)
     * @return array
     */
    public function getStoneColorMap()
    {
        return [
            18 => [132, 632, 645],//D
            19 => [332, 633, 646],//E
            22 => [133, 634, 647],//F
            50 => [134, 635, 648],//G
            444 => ["", "", ""],//GH
            51 => [135, 636, 649],//H
            52 => [136, 637, 650],//I
            53 => [137, 639, 652],//J
            447 => ["", "", 651],//IJ
            153 => [131, 640, 653],//K
            154 => [331, 641, 654],//L
            155 => [333, 642, 655],//M
            156 => [334, 643, 656],//N
            242 => [335, 644, 657],//不分级
            157 => ["", "", ""],//其它
        ];
    }

    /**
     * 净度
     * 主石 => (副石1, 副石2, 副石3)
     * @return array
     */
    public function getStoneClarityMap()
    {
        return [
            6 => [123, 605, 618],//FL
            7 => [124, 606, 619],//IF
            8 => [125, 607, 620],//VVS1
            62 => [126, 608, 621],//VVS2
            63 => [127, 609, 622],//VS1
            64 => [128, 610, 623],//VS2
            65 => [129, 611, 624],//SI1
            66 => [130, 612, 626],//SI2
            448 => [327, 613, 625],//SI
            324 => [328, 614, 627],//P1
            325 => [328, 615, 628],//P2
            326 => [329, 616, 629],//P3
            243 => [330, 617, 630],//不分级
        ];
    }

    /**
     * 切工
     * 主石 => (副石1, 副石2)
     * @return array
     */
    public function getStoneCutMap()
    {
        return [
            337 => [479],//Poor
            336 => [478],//Fair
            13 => [475],//EX
            14 => [476],//VG
            15 => [477],//GD
        ];
    }

    /**
     * 色彩
     * 主石 => [副石1, 副石2]
     * @return array
     */
    public function getStoneColourMap()
    {
        return [
            396 => [404, 412],//白色
            397 => [405, 413],//黑色
            398 => [406, 414],//红色
            399 => [407, 415],//绿色
            400 => [408, 416],//蓝色
            401 => [409, 417],//红色
            402 => [411, 418],//粉色
            403 => [411, 419],//紫色
        ];
    }
}