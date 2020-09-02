<?php

namespace addons\Warehouse\services;

use Yii;
use common\components\Service;
use common\helpers\SnHelper;
use addons\Warehouse\common\models\WarehouseBill;
use addons\Warehouse\common\models\WarehouseBillGoodsL;
use addons\Warehouse\common\forms\WarehouseBillTForm;
use addons\Warehouse\common\forms\WarehouseBillTGoodsForm;
use addons\Style\common\models\Style;
use addons\Style\common\models\Qiban;
use addons\Warehouse\common\enums\PeiJianWayEnum;
use addons\Warehouse\common\enums\PeiLiaoWayEnum;
use addons\Warehouse\common\enums\PeiShiWayEnum;
use addons\Style\common\enums\JintuoTypeEnum;
use addons\Style\common\enums\QibanTypeEnum;
use addons\Style\common\enums\AttrIdEnum;
use common\enums\AuditStatusEnum;
use common\helpers\UploadHelper;
use common\enums\StatusEnum;

/**
 * 其他收货单
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
            ->select(['sum(1) as goods_num', 'sum(cost_price) as total_cost', 'sum(market_price) as total_market'])
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
            $goodsInfo[$i]['is_wholesale'] = $form->is_wholesale;//批发
            $goodsInfo[$i]['auto_goods_id'] = $form->auto_goods_id;
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
        $error = $saveData = [];
        $bill = WarehouseBill::findOne($form->bill_id);
        while ($goods = fgetcsv($file)) {
            if ($i == 0) {
                $i++;
                continue;
            }
            if (count($goods) != 75) {
                throw new \Exception("模板格式不正确，请下载最新模板");
            }
            $goods = $form->trimField($goods);
            $goods_id = $goods[0] ?? "";
            if (empty($goods_id)) {
                $goods_id = SnHelper::createGoodsId();
            }
            $style_sn = $goods[1] ?? "";
            if (empty($style_sn)) {
                $flag = false;
                $error[$i][] = "款号不能为空";
            }
            $style = Style::findOne(['style_sn' => $style_sn, 'audit_status' => AuditStatusEnum::UNPASS]);
            if (empty($style)) {
                $flag = false;
                $error[$i][] = "款号不存在或未审核";
            }
            $qiban_sn = $goods[2] ?? "";
            $goods_name = $goods[3] ?? "";
            $material_type = $goods[4] ?? "";
            if (!empty($material_type)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $material_type, AttrIdEnum::MATERIAL_TYPE);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "材质录入值不对或该款[" . $style_sn . "]材质不支持[" . $material_type . "]请前往款式库核实";
                } else {
                    $material_type = $attr_id;
                }
            }
            $material_color = $goods[5] ?? "";
            if (!empty($material_color)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $material_color, AttrIdEnum::MATERIAL_COLOR);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "材质颜色录入值不对或该款[" . $style_sn . "]材质颜色不支持[" . $material_type . "]请前往款式库核实";
                } else {
                    $material_type = $attr_id;
                }
            }
            $finger_hk = $goods[6] ?? "";
            if (!empty($finger_hk)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $finger_hk, AttrIdEnum::PORT_NO);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "手寸(港号)录入值不对或该款[" . $style_sn . "]手寸(港号)不支持[" . $finger_hk . "]请前往款式库核实";
                } else {
                    $finger_hk = $attr_id;
                }
            }
            $finger = $goods[7] ?? "";
            if (!empty($finger)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $finger, AttrIdEnum::FINGER);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "手寸(美号)录入值不对或该款[" . $style_sn . "]手寸(美号)不支持[" . $finger . "]请前往款式库核实";
                } else {
                    $finger = $attr_id;
                }
            }
            $length = $goods[8] ?? "";
            $product_size = $goods[9] ?? "";
            $xiangkou = $goods[10] ?? "";
            if (!empty($xiangkou)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $xiangkou, AttrIdEnum::XIANGKOU);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "镶口录入值不对或该款[" . $style_sn . "]镶口不支持[" . $xiangkou . "]请前往款式库核实";
                } else {
                    $xiangkou = $attr_id;
                }
            }
            $kezi = $goods[11] ?? "";
            $chain_type = $goods[12] ?? "";
            if (!empty($chain_type)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $chain_type, AttrIdEnum::CHAIN_TYPE);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "链类型录入值不对或该款[" . $style_sn . "]链类型不支持[" . $chain_type . "]请前往款式库核实";
                } else {
                    $chain_type = $attr_id;
                }
            }
            $cramp_ring = $goods[13] ?? "";
            if (!empty($cramp_ring)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $cramp_ring, AttrIdEnum::CHAIN_BUCKLE);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "扣环录入值不对或该款[" . $style_sn . "]扣环不支持[" . $cramp_ring . "]请前往款式库核实";
                } else {
                    $cramp_ring = $attr_id;
                }
            }
            $talon_head_type = $goods[14] ?? "";
            if (!empty($talon_head_type)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $talon_head_type, AttrIdEnum::TALON_HEAD_TYPE);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "爪头形状录入值不对或该款[" . $style_sn . "]爪头形状不支持[" . $talon_head_type . "]请前往款式库核实";
                } else {
                    $talon_head_type = $attr_id;
                }
            }
            $peiliao_way = $goods[15] ?? "";
            if (!empty($peiliao_way)) {
                $peiliao_way = \addons\Warehouse\common\enums\PeiLiaoWayEnum::getIdByName($peiliao_way);
                if (empty($peiliao_way)) {
                    $flag = false;
                    $error[$i][] = "配料方式录入值不对";
                }
            }
            $suttle_weight = $goods[16] ?? "";
            $gold_weight = $goods[17] ?? "";
            $gold_loss = $goods[18] ?? "";
            $gold_price = $goods[19] ?? "";
            $main_pei_type = $goods[20] ?? "";
            if (!empty($main_pei_type)) {
                $main_pei_type = \addons\Warehouse\common\enums\PeiShiWayEnum::getIdByName($main_pei_type);
                if (empty($main_pei_type)) {
                    $flag = false;
                    $error[$i][] = "主石配石方式录入值不对";
                }
            }
            $main_stone_sn = $goods[21] ?? "";
            $main_stone_type = $goods[22] ?? "";
            if (!empty($main_stone_type)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $main_stone_type, AttrIdEnum::MAIN_STONE_TYPE);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "主石类型录入值不对或该款[" . $style_sn . "]主石类型不支持[" . $main_stone_type . "]请前往款式库核实";
                } else {
                    $main_stone_type = $attr_id;
                }
            }
            $main_stone_num = $goods[23] ?? "";
            $main_stone_weight = $goods[24] ?? "";
            $main_stone_price = $goods[25] ?? "";
            $main_stone_shape = $goods[26] ?? "";
            if (!empty($main_stone_shape)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $main_stone_shape, AttrIdEnum::MAIN_STONE_CLARITY);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "主石形状录入值不对或该款[" . $style_sn . "]主石形状不支持[" . $main_stone_shape . "]请前往款式库核实";
                } else {
                    $main_stone_shape = $attr_id;
                }
            }
            $main_stone_color = $goods[27] ?? "";
            if (!empty($second_stone_shape1)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $main_stone_color, AttrIdEnum::MAIN_STONE_COLOR);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "主石颜色录入值不对或该款[" . $style_sn . "]主石颜色不支持[" . $second_stone_shape1 . "]请前往款式库核实";
                } else {
                    $second_stone_shape1 = $attr_id;
                }
            }
            $main_stone_clarity = $goods[28] ?? "";
            if (!empty($main_stone_clarity)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $main_stone_clarity, AttrIdEnum::MAIN_STONE_CLARITY);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "主石净度录入值不对或该款[" . $style_sn . "]主石净度不支持[" . $main_stone_clarity . "]请前往款式库核实";
                } else {
                    $main_stone_clarity = $attr_id;
                }
            }
            $main_stone_cut = $goods[29] ?? "";
            if (!empty($main_stone_cut)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $main_stone_cut, AttrIdEnum::MAIN_STONE_CUT);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "主石切工录入值不对或该款[" . $style_sn . "]主石切工不支持[" . $main_stone_cut . "]请前往款式库核实";
                } else {
                    $main_stone_cut = $attr_id;
                }
            }
            $main_stone_colour = $goods[30] ?? "";
            if (!empty($main_stone_colour)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $main_stone_colour, AttrIdEnum::MAIN_STONE_COLOUR);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "主石色彩录入值不对或该款[" . $style_sn . "]主石色彩不支持[" . $main_stone_colour . "]请前往款式库核实";
                } else {
                    $main_stone_colour = $attr_id;
                }
            }
            $main_stone_size = $goods[31] ?? "";
            $second_pei_type = $goods[32] ?? "";
            if (!empty($second_pei_type)) {
                $second_pei_type = \addons\Warehouse\common\enums\PeiShiWayEnum::getIdByName($second_pei_type);
                if (empty($second_pei_type)) {
                    $flag = false;
                    $error[$i][] = "副石1配石方式录入值不对";
                }
            }
            $second_stone_sn1 = $goods[33] ?? "";
            $second_stone_type1 = $goods[34] ?? "";
            if (!empty($second_stone_type1)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $second_stone_type1, AttrIdEnum::SIDE_STONE1_TYPE);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "副石1类型录入值不对或该款[" . $style_sn . "]副石1类型不支持[" . $second_stone_type1 . "]请前往款式库核实";
                } else {
                    $second_stone_type1 = $attr_id;
                }
            }
            $second_stone_num1 = $goods[35] ?? "";
            $second_stone_weight1 = $goods[36] ?? "";
            $second_stone_price1 = $goods[37] ?? "";
            $second_stone_shape1 = $goods[38] ?? "";
            if (!empty($second_stone_shape1)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $second_stone_shape1, AttrIdEnum::SIDE_STONE1_SHAPE);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "副石1形状录入值不对或该款[" . $style_sn . "]副石1形状不支持[" . $second_stone_shape1 . "]请前往款式库核实";
                } else {
                    $second_stone_shape1 = $attr_id;
                }
            }
            $second_stone_color1 = $goods[39] ?? "";
            if (!empty($second_stone_color1)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $second_stone_color1, AttrIdEnum::SIDE_STONE1_COLOR);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "副石1颜色录入值不对或该款[" . $style_sn . "]副石1颜色不支持[" . $second_stone_color1 . "]请前往款式库核实";
                } else {
                    $second_stone_color1 = $attr_id;
                }
            }
            $second_stone_clarity1 = $goods[40] ?? "";
            if (!empty($second_stone_clarity1)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $second_stone_clarity1, AttrIdEnum::SIDE_STONE1_CLARITY);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "副石1净度录入值不对或该款[" . $style_sn . "]副石1净度不支持[" . $second_stone_clarity1 . "]请前往款式库核实";
                } else {
                    $second_stone_clarity1 = $attr_id;
                }
            }
            $second_stone_colour1 = $goods[41] ?? "";
            if (!empty($second_stone_colour1)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $second_stone_colour1, AttrIdEnum::SIDE_STONE1_COLOUR);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "副石1色彩录入值不对或该款[" . $style_sn . "]副石1色彩不支持[" . $second_stone_colour1 . "]请前往款式库核实";
                } else {
                    $second_stone_colour1 = $attr_id;
                }
            }
            $second_pei_type2 = $goods[42] ?? "";
            if (!empty($second_pei_type2)) {
                $second_pei_type2 = \addons\Warehouse\common\enums\PeiShiWayEnum::getIdByName($second_pei_type2);
                if (empty($second_pei_type2)) {
                    $flag = false;
                    $error[$i][] = "副石2配石方式录入值不对";
                }
            }
            $second_stone_sn2 = $goods[43] ?? "";
            $second_stone_type2 = $goods[44] ?? "";
            if (!empty($second_stone_type2)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $second_stone_type2, AttrIdEnum::SIDE_STONE2_TYPE);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "副石2类型录入值不对或该款[" . $style_sn . "]副石2类型不支持[" . $second_stone_type2 . "]请前往款式库核实";
                } else {
                    $second_stone_type2 = $attr_id;
                }
            }
            $second_stone_num2 = $goods[45] ?? "";
            $second_stone_weight2 = $goods[46] ?? "";
            $second_stone_price2 = $goods[47] ?? "";
            $second_stone_shape2 = $goods[48] ?? "";
            if (!empty($second_stone_shape2)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $second_stone_shape2, AttrIdEnum::SIDE_STONE2_SHAPE);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "副石2形状录入值不对或该款[" . $style_sn . "]副石2形状不支持[" . $second_stone_shape2 . "]请前往款式库核实";
                } else {
                    $second_stone_shape2 = $attr_id;
                }
            }
            $second_stone_size2 = $goods[49] ?? "";
            $stone_remark = $goods[50] ?? "";
            $parts_way = $goods[51] ?? "";
            if (!empty($parts_way)) {
                $parts_way = \addons\Warehouse\common\enums\PeiJianWayEnum::getIdByName($parts_way);
                if (empty($parts_way)) {
                    $flag = false;
                    $error[$i][] = "配件方式录入值不对";
                }
            }
            $parts_type = $goods[52] ?? "";
            if (!empty($parts_type)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $parts_type, AttrIdEnum::MAT_PARTS_TYPE);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "配件类型录入值不对或该款[" . $style_sn . "]配件类型不支持[" . $parts_type . "]请前往款式库核实";
                } else {
                    $parts_type = $attr_id;
                }
            }
            $parts_material = $goods[53] ?? "";
            if (!empty($parts_material)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $parts_material, AttrIdEnum::MATERIAL_TYPE);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "配件材质录入值不对或该款[" . $style_sn . "]配件材质不支持[" . $parts_material . "]请前往款式库核实";
                } else {
                    $parts_material = $attr_id;
                }
            }
            $parts_num = $goods[54] ?? "";
            $parts_gold_weight = $goods[55] ?? "";
            $parts_price = $goods[56] ?? "";
            $peishi_num = $goods[57] ?? "";
            $peishi_weight = $goods[58] ?? "";
            $peishi_gong_fee = $goods[59] ?? "";
            $parts_fee = $goods[60] ?? "";
            $gong_fee = $goods[61] ?? "";
            $xiangqian_craft = $goods[62] ?? "";
            if (!empty($xiangqian_craft)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $xiangqian_craft, AttrIdEnum::MATERIAL_TYPE);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "镶嵌工艺录入值不对或该款[" . $style_sn . "]镶嵌工艺不支持[" . $xiangqian_craft . "]请前往款式库核实";
                } else {
                    $xiangqian_craft = $attr_id;
                }
            }
            $xianqian_price = $goods[63] ?? "";
            $biaomiangongyi = $goods[64] ?? "";
            if (!empty($biaomiangongyi)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $biaomiangongyi, AttrIdEnum::FACEWORK);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "表面工艺录入值不对或该款[" . $style_sn . "]表面工艺不支持[" . $biaomiangongyi . "]请前往款式库核实";
                } else {
                    $biaomiangongyi = $attr_id;
                }
            }
            $biaomiangongyi_fee = $goods[65] ?? "";
            $fense_fee = $goods[66] ?? "";
            $penlasha_fee = $goods[67] ?? "";
            $bukou_fee = $goods[68] ?? "";
            $templet_fee = $goods[69] ?? "";
            $cert_fee = $goods[70] ?? "";
            $other_fee = $goods[71] ?? "";
            $main_cert_id = $goods[72] ?? "";
            $main_cert_type = $goods[73] ?? "";
            if (!empty($main_cert_type)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $main_cert_type, AttrIdEnum::DIA_CERT_TYPE);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "主石证书类型录入值不对或该款[" . $style_sn . "]主石证书类型不支持[" . $main_cert_type . "]请前往款式库核实";
                } else {
                    $main_cert_type = $attr_id;
                }
            }
            $markup_rate = $goods[74] ?? "";
            $jintuo_type = $goods[75] ?? "";
            if (!empty($jintuo_type)) {
                $jintuo_type = JintuoTypeEnum::getIdByName($jintuo_type);
                if (empty($jintuo_type)) {
                    $flag = false;
                    $error[$i][] = "金托类型录入值不对";
                }
            }
            $remark = $goods[76] ?? "";
            $saveData[] = $item =  [
                'bill_id' => $bill->id,
                'bill_no' => $bill->bill_no,
                'bill_type' => $bill->bill_type,
                'goods_id' => $goods_id,
                'style_sn' => $style_sn,
                'qiban_sn' => $qiban_sn,
                'goods_name' => $goods_name,
                'material_type' => $material_type,
                'finger_hk' => $finger_hk,
                'finger' => $finger,
                'length' => $length,
                'product_size' => $product_size,
                'xiangkou' => $xiangkou,
                'kezi' => $kezi,
                'chain_type' => $chain_type,
                'cramp_ring' => $cramp_ring,
                'talon_head_type' => $talon_head_type,
                'peiliao_way' => $peiliao_way,
                'suttle_weight' => $suttle_weight,
                'gold_weight' => $gold_weight,
                'gold_loss' => $gold_loss,
                'gold_price' => $gold_price,
                'main_pei_type' => $main_pei_type,
                'main_stone_sn' => $main_stone_sn,
                'main_stone_type' => $main_stone_type,
                'main_stone_num' => $main_stone_num,
                'main_stone_weight' => $main_stone_weight,
                'main_stone_price' => $main_stone_price,
                'main_stone_shape' => $main_stone_shape,
                'main_stone_color' => $main_stone_color,
                'main_stone_clarity' => $main_stone_clarity,
                'main_stone_cut' => $main_stone_cut,
                'main_stone_colour' => $main_stone_colour,
                'main_stone_size' => $main_stone_size,
                'second_pei_type' => $second_pei_type,
                'second_stone_sn1' => $second_stone_sn1,
                'second_stone_type1' => $second_stone_type1,
                'second_stone_num1' => $second_stone_num1,
                'second_stone_weight1' => $second_stone_weight1,
                'second_stone_price1' => $second_stone_price1,
                'second_stone_shape1' => $second_stone_shape1,
                'second_stone_color1' => $second_stone_color1,
                'second_stone_clarity1' => $second_stone_clarity1,
                'second_stone_colour1' => $second_stone_colour1,
                'second_pei_type2' => $second_pei_type2,
                'second_stone_sn2' => $second_stone_sn2,
                'second_stone_type2' => $second_stone_type2,
                'second_stone_num2' => $second_stone_num2,
                'second_stone_weight2' => $second_stone_weight2,
                'second_stone_price2' => $second_stone_price2,
                'second_stone_shape2' => $second_stone_shape2,
                'second_stone_size2' => $second_stone_size2,
                'stone_remark' => $stone_remark,
                'parts_way' => $parts_way,
                'parts_type' => $parts_type,
                'parts_material' => $parts_material,
                'parts_num' => $parts_num,
                'parts_gold_weight' => $parts_gold_weight,
                'parts_price' => $parts_price,
                'peishi_num' => $peishi_num,
                'peishi_weight' => $peishi_weight,
                'peishi_gong_fee' => $peishi_gong_fee,
                'parts_fee' => $parts_fee,
                'gong_fee' => $gong_fee,
                'xiangqian_craft' => $xiangqian_craft,
                'xianqian_price' => $xianqian_price,
                'biaomiangongyi' => $biaomiangongyi,
                'biaomiangongyi_fee' => $biaomiangongyi_fee,
                'fense_fee' => $fense_fee,
                'penlasha_fee' => $penlasha_fee,
                'bukou_fee' => $bukou_fee,
                'templet_fee' => $templet_fee,
                'cert_fee' => $cert_fee,
                'other_fee' => $other_fee,
                'main_cert_id' => $main_cert_id,
                'main_cert_type' => $main_cert_type,
                'markup_rate' => $markup_rate,
                'jintuo_type' => $jintuo_type,
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
            }
        }

        if (!$flag) {
            //发生错误
            $message = '';
            foreach ($error as $k => $v) {
                $s = "【" . implode('】,【', $v) . '】';
                $message .= '第' . ($k + 1) . '行' . $s . '<hr>';
            }
            throw new \Exception($message);
        }

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

        $this->syncUpdatePriceAll($bill);
    }

    /**
     *
     * 同步更新单据商品价格
     * @param WarehouseBillTForm $form
     * @return object
     * @throws
     */
    public function syncUpdatePriceAll($form)
    {
        $goods = WarehouseBillTGoodsForm::findAll(['bill_id' => $form->id]);
        if (!empty($goods)) {
            foreach ($goods as $good) {
                $this->syncUpdatePrice($good);
            }
        }
        return $goods;
    }

    /**
     *
     * 含耗重=(净重*(1+损耗))
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateLossWeight($form)
    {
        return bcmul($form->suttle_weight, 1 + ($form->gold_loss / 100), 3) ?? 0;
    }

    /**
     *
     * 金料额=(金价*净重*(1+损耗))
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateGoldAmount($form)
    {
        return bcmul($form->gold_price, $this->calculateLossWeight($form), 3) ?? 0;
    }

    /**
     *
     * 主石总重=(主石单颗重*主石数量)
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateMainStoneWeight($form)
    {
        return bcmul($form->main_stone_weight, $form->main_stone_num, 3) ?? 0;
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
        return bcmul($this->calculateMainStoneWeight($form), $form->main_stone_price, 3) ?? 0;
    }

    /**
     *
     * 副石总重=(副石1重+副石2重+副石3重)
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateSecondStoneWeight($form)
    {
        return bcadd(bcadd($form->second_stone_weight1, $form->second_stone_weight2, 3), $form->second_stone_weight3, 3) ?? 0;
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
        return bcmul($form->second_stone_weight1, $form->second_stone_price1, 3) ?? 0;
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
        return bcmul($form->second_stone_weight2, $form->second_stone_price2, 3) ?? 0;
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
        return bcmul($form->second_stone_weight3, $form->second_stone_price3, 3) ?? 0;
    }

    /**
     *
     * 配件额=(配件重*配件金价)
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculatePartsAmount($form)
    {
        return bcmul($form->parts_gold_weight, $form->parts_price, 3) ?? 0;
    }

    /**
     *
     * 配石费=((副石重/数量)小于0.03ct的，*数量*配石工费)[配石费=(配石数量*配石重量*配石工费/ct)]
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculatePeishiFee($form)
    {
        return bcmul(bcmul($form->peishi_num, $form->peishi_weight, 3), $form->peishi_gong_fee) ?? 0;
    }

    /**
     *
     * 基本工费=(克/工费*含耗重)
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateBasicGongFee($form)
    {
        return bcmul($form->gong_fee, $this->calculateLossWeight($form), 3) ?? 0;
    }

    /**
     *
     * 总副石数量=(副石1数量+副石2数量+副石3数量)
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateSecondStoneNum($form)
    {
        return bcadd(bcadd($form->second_stone_num1, $form->second_stone_num2, 3), $form->second_stone_num3, 3) ?? 0;
    }

    /**
     *
     * 镶石费=(镶石单价*总副石数量)
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateXiangshiFee($form)
    {
        return bcmul($form->xianqian_price, $this->calculateSecondStoneNum($form), 3) ?? 0;
    }

    /**
     *
     * 总工费=(配石费+基本工费+配件工费+镶石费+表面工艺费+分色分件费+喷拉砂费+补口费+版费+证书费+其他费用)
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateTotalGongFee($form)
    {
        $total_gong_fee = 0;
        $total_gong_fee = bcadd($total_gong_fee, $this->calculatePeishiFee($form), 3);
        $total_gong_fee = bcadd($total_gong_fee, $this->calculateBasicGongFee($form), 3);
        $total_gong_fee = bcadd($total_gong_fee, $form->parts_fee, 3);
        $total_gong_fee = bcadd($total_gong_fee, $this->calculateXiangshiFee($form), 3);
        $total_gong_fee = bcadd($total_gong_fee, $form->biaomiangongyi_fee, 3);//表面工艺费
        $total_gong_fee = bcadd($total_gong_fee, $form->fense_fee, 3);//分件/分色费
        $total_gong_fee = bcadd($total_gong_fee, $form->penlasha_fee, 3);//喷拉砂费
        $total_gong_fee = bcadd($total_gong_fee, $form->bukou_fee, 3);//补口费
        //$total_gong_fee = bcadd($total_gong_fee, $form->extra_stone_fee, 3);//超石费
        $total_gong_fee = bcadd($total_gong_fee, $form->templet_fee, 3);//样板工费
        $total_gong_fee = bcadd($total_gong_fee, $form->cert_fee, 3);//证书费
        $total_gong_fee = bcadd($total_gong_fee, $form->other_fee, 3);//其他补充费用

        return sprintf("%.2f", $total_gong_fee) ?? 0;
    }

    /**
     *
     * 工厂成本=(主副石成本(厂配)+总工费)
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateFactoryCost($form)
    {
        $factory_cost = 0;
        if ($form->peiliao_way == PeiLiaoWayEnum::FACTORY) {
            $factory_cost = bcadd($factory_cost, $this->calculateGoldAmount($form), 3);
        }
        if ($form->main_pei_type == PeiShiWayEnum::FACTORY) {
            $factory_cost = bcadd($factory_cost, $this->calculateMainStoneCost($form), 3);
        }
        if ($form->second_pei_type == PeiShiWayEnum::FACTORY) {
            $factory_cost = bcadd($factory_cost, $this->calculateSecondStone1Cost($form), 3);
        }
        if ($form->second_pei_type2 == PeiShiWayEnum::FACTORY) {
            $factory_cost = bcadd($factory_cost, $this->calculateSecondStone2Cost($form), 3);
        }
        if ($form->second_pei_type3 == PeiShiWayEnum::FACTORY) {
            $factory_cost = bcadd($factory_cost, $this->calculateSecondStone3Cost($form), 3);
        }
        if ($form->parts_way == PeiJianWayEnum::FACTORY) {
            $factory_cost = bcadd($factory_cost, $this->calculatePartsAmount($form), 3);
        }
        $factory_cost = bcadd($factory_cost, $this->calculateTotalGongFee($form), 3);//总工费

        return sprintf("%.2f", $factory_cost) ?? 0;
    }

    /**
     *
     * 公司成本(成本价)=(金料额+主石成本+副石1成本+副石2成本+副石3成本+配件额+总工费)
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateCostPrice($form)
    {
        $cost_price = 0;
        $cost_price = bcadd($cost_price, $this->calculateGoldAmount($form), 3);
        $cost_price = bcadd($cost_price, $this->calculateMainStoneCost($form), 3);
        $cost_price = bcadd($cost_price, $this->calculateSecondStone1Cost($form), 3);
        $cost_price = bcadd($cost_price, $this->calculateSecondStone2Cost($form), 3);
        $cost_price = bcadd($cost_price, $this->calculateSecondStone3Cost($form), 3);
        $cost_price = bcadd($cost_price, $this->calculatePartsAmount($form), 3);
        $cost_price = bcadd($cost_price, $this->calculateTotalGongFee($form), 3);

        return sprintf("%.2f", $cost_price) ?? 0;
    }

    /**
     *
     * 标签价(市场价)=(公司成本*倍率)
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculateMarketPrice($form)
    {
        return bcmul($form->markup_rate, $this->calculateCostPrice($form), 3) ?? 0;
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
        $form->lncl_loss_weight = $this->calculateLossWeight($form);//含耗重
        $form->gold_amount = $this->calculateGoldAmount($form);//金料额
        $form->main_stone_amount = $this->calculateMainStoneCost($form);//主石成本
        $form->second_stone_amount1 = $this->calculateSecondStone1Cost($form);//副石1成本
        $form->second_stone_amount2 = $this->calculateSecondStone2Cost($form);//副石2成本
        $form->second_stone_amount3 = $this->calculateSecondStone3Cost($form);//副石3成本
        $form->peishi_fee = $this->calculatePeishiFee($form);//配石费
        $form->xianqian_fee = $this->calculateXiangshiFee($form);//镶石费
        $form->parts_amount = $this->calculatePartsAmount($form);//配件额
        $form->basic_gong_fee = $this->calculateBasicGongFee($form);//基本工费
        $form->total_gong_fee = $this->calculateTotalGongFee($form);//总工费
        $form->factory_cost = $this->calculateFactoryCost($form);//工厂成本
        $form->cost_price = $this->calculateCostPrice($form);//公司成本
        $form->market_price = $this->calculateMarketPrice($form);//标签价
        if (false === $form->save()) {
            throw new \Exception($this->getError($form));
        }
        return $form;
    }

}