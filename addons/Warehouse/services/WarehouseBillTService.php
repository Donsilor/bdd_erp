<?php

namespace addons\Warehouse\services;

use Yii;
use common\components\Service;
use common\helpers\SnHelper;
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
use addons\Style\common\enums\JintuoTypeEnum;
use addons\Style\common\enums\QibanTypeEnum;
use addons\Style\common\enums\AttrIdEnum;
use common\enums\AuditStatusEnum;
use common\helpers\UploadHelper;
use common\enums\StatusEnum;
use common\helpers\StringHelper;
use yii\helpers\Url;

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
        $error_off = true;
        $error = $saveData = [];
        $bill = WarehouseBill::findOne($form->bill_id);
        while ($goods = fgetcsv($file)) {
            if ($i <= 1) {
                $i++;
                continue;
            }
            if (count($goods) != 74) {
                throw new \Exception("模板格式不正确，请下载最新模板");
            }
            $goods = $form->trimField($goods);
            $goods_id = $goods[0] ?? "";
            $auto_goods_id = 1;//是否自动货号 默认手填
            if (empty($goods_id)) {
                $goods_id = SnHelper::createGoodsId();
                $auto_goods_id = 0;
            }
            $style_sn = $goods[1] ?? "";
            $qiban_sn = $goods[2] ?? "";
            if (!empty($style_sn)) {
                $error[$i][] = "[" . $style_sn . "]";
            } else {
                $error[$i][] = "[" . $qiban_sn . "]";
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
            if ($qiban_type != QibanTypeEnum::NO_STYLE) {
                if (empty($style_sn)) {
                    $flag = false;
                    $error[$i][] = "款号不能为空";
                    if (!$flag) {
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
            $goods_num = 1;
            $goods_name = $goods[3] ?? "";
            $material_type = $goods[4] ?? "";
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
            $material_color = $goods[5] ?? "";
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
            $finger_hk = $goods[6] ?? "";
            if (!empty($finger_hk)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $finger_hk, AttrIdEnum::PORT_NO);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "手寸(港号)：[" . $finger_hk . "]录入值有误";
                    $finger_hk = "";
                } else {
                    $finger_hk = $attr_id;
                }
            }
            $finger = $goods[7] ?? "";
            if (!empty($finger)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $finger, AttrIdEnum::FINGER);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "手寸(美号)：[" . $finger . "]录入值有误";
                    $finger = "";
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
                    $error[$i][] = "镶口：[" . $xiangkou . "]录入值有误";
                    $xiangkou = "";
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
                    $error[$i][] = "链类型：[" . $chain_type . "]录入值有误";
                    $chain_type = "";
                } else {
                    $chain_type = $attr_id;
                }
            }
            $cramp_ring = $goods[13] ?? "";
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
            $talon_head_type = $goods[14] ?? "";
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
            $peiliao_way = $form->formatValue($goods[15], 0) ?? "";
            if (!empty($peiliao_way)) {
                $peiliao_way = \addons\Warehouse\common\enums\PeiLiaoWayEnum::getIdByName($peiliao_way);
                if (empty($peiliao_way) && $peiliao_way === "") {
                    $flag = false;
                    $error[$i][] = "配料方式：[" . $peiliao_way . "]录入值有误";
                    $peiliao_way = 0;
                }
            }
            $suttle_weight = $form->formatValue($goods[16], 0) ?? 0;
            $gold_weight = $form->formatValue($goods[17], 0) ?? 0;
            $gold_loss = $form->formatValue($goods[18], 0) ?? 0;
            $gold_price = $form->formatValue($goods[19], 0) ?? 0;
            $main_pei_type = $form->formatValue($goods[20], 0) ?? 0;
            $main_stone_sn = $goods[21] ?? "";
            $stone = null;
            $cert_id = $cert_type = "";
            if (!empty($main_stone_sn)) {
                $stone = WarehouseStone::findOne(['stone_sn' => $main_stone_sn]);
                if (empty($stone)) {
                    $flag = false;
                    $error[$i][] = "主石编号：[" . $main_stone_sn . "]录入值有误";
                } else {
                    $cert_id = $stone->cert_id ?? "";
                    $cert_type = $stone->cert_type ?? "";
                }
            }
            $main_stone_type = $goods[22] ?? "";
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
                $main_stone_type = $stone->stone_type ?? "";
            }
            $main_stone_num = $form->formatValue($goods[23], 0) ?? 0;
            $main_stone_weight = $form->formatValue($goods[24], 0) ?? 0;
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
            $main_stone_price = $form->formatValue($goods[25], 0) ?? 0;
            $main_stone_shape = $goods[26] ?? "";
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
                $main_stone_shape = $stone->stone_shape ?? "";
            }
            $main_stone_color = $goods[27] ?? "";
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
                $main_stone_color = $stone->stone_color ?? "";
            }
            $main_stone_clarity = $goods[28] ?? "";
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
                $main_stone_clarity = $stone->stone_clarity ?? "";
            }
            $main_stone_cut = $goods[29] ?? "";
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
                $main_stone_cut = $stone->stone_cut ?? "";
            }
            $main_stone_colour = $goods[30] ?? "";
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
                $main_stone_colour = $stone->stone_colour ?? "";
            }
//            $main_stone_size = $goods[31] ?? "";
//            if (empty($main_stone_size)) {
//                $main_stone_size = $stone->stone_size ?? "";
//            }
            $second_pei_type = $form->formatValue($goods[31], 0) ?? 0;
            $second_stone_sn1 = $goods[32] ?? "";
            $stone = null;
            if (!empty($second_stone_sn1)) {
                $stone = WarehouseStone::findOne(['stone_sn' => $second_stone_sn1]);
                if (empty($stone)) {
                    $flag = false;
                    $error[$i][] = "副石1编号：[" . $second_stone_sn1 . "]录入值有误";
                }
            }
            $second_stone_type1 = $goods[33] ?? "";
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
                $second_stone_type1 = $stone->stone_type ?? "";
            }
            $second_stone_num1 = $form->formatValue($goods[34], 0) ?? 0;
            $second_stone_weight1 = $form->formatValue($goods[35], 0) ?? 0;
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
            $second_stone_price1 = $form->formatValue($goods[36], 0) ?? 0;
            $second_stone_shape1 = $goods[37] ?? "";
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
                $second_stone_shape1 = $stone->stone_shape ?? "";
            }
            $second_stone_color1 = $goods[38] ?? "";
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
                $second_stone_color1 = $stone->stone_color ?? "";
            }
            $second_stone_clarity1 = $goods[39] ?? "";
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
                $second_stone_clarity1 = $stone->stone_clarity ?? "";
            }
            $second_stone_cut1 = $goods[40] ?? "";
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
                $second_stone_cut1 = $stone->stone_cut ?? "";
            }
            $second_stone_colour1 = $goods[41] ?? "";
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
                $second_stone_colour1 = $stone->stone_colour ?? "";
            }
            $second_pei_type2 = $form->formatValue($goods[42], 0) ?? 0;
            $second_stone_sn2 = $goods[43] ?? "";
            $stone = null;
            if (!empty($second_stone_sn2)) {
                $stone = WarehouseStone::findOne(['stone_sn' => $second_stone_sn2]);
                if (empty($stone)) {
                    $flag = false;
                    $error[$i][] = "副石2编号：[" . $second_stone_sn2 . "]录入值有误";
                }
            }
            $second_stone_type2 = $goods[44] ?? "";
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
                $second_stone_type2 = $stone->stone_type ?? "";
            }
            $second_stone_num2 = $form->formatValue($goods[45], 0) ?? 0;
            $second_stone_weight2 = $form->formatValue($goods[46], 0) ?? 0;
            if (!empty($second_pei_type2)) {
                $second_pei_type2 = \addons\Warehouse\common\enums\PeiShiWayEnum::getIdByName($second_pei_type2);
                if (empty($second_pei_type2) && $second_pei_type2 === "") {
                    $flag = false;
                    $error[$i][] = "副石2配石方式：录入值有误";
                    $second_pei_type2 = 0;
                }
            } else {
                $second_pei_type = $form->getPeiType($second_stone_sn2, $second_stone_num2, $second_stone_weight2);
            }
            $second_stone_price2 = $form->formatValue($goods[47], 0) ?? 0;
//            $second_stone_shape2 = $goods[48] ?? "";
//            if (!empty($second_stone_shape2)) {
//                $attr_id = $form->getAttrIdByAttrValue($style_sn, $second_stone_shape2, AttrIdEnum::SIDE_STONE2_SHAPE);
//                if (empty($attr_id)) {
//                    $flag = false;
//                    $error[$i][] = "副石2形状录入值不对或该款[" . $goods_sn . "]副石2形状不支持[" . $second_stone_shape2 . "]请前往款式库核实";
//                } else {
//                    $second_stone_shape2 = $attr_id;
//                }
//            } elseif (!empty($stone)) {
//                $second_stone_shape2 = $stone->stone_shape ?? "";
//            }
//            $second_stone_size2 = $goods[49] ?? "";
//            if (empty($second_stone_size2)) {
//                $second_stone_size2 = $stone->stone_size ?? "";
//            }
            $stone_remark = $goods[48] ?? "";
            $parts_way = $form->formatValue($goods[49], 0) ?? "";
            if (!empty($parts_way)) {
                $parts_way = \addons\Warehouse\common\enums\PeiJianWayEnum::getIdByName($parts_way);
                if (empty($parts_way) && $parts_way === "") {
                    $flag = false;
                    $error[$i][] = "配件方式：录入值有误";
                    $parts_way = 0;
                }
            }
            $parts_type = $goods[50] ?? "";
            if (!empty($parts_type)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $parts_type, AttrIdEnum::MAT_PARTS_TYPE);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "配件类型：[" . $parts_type . "]录入值有误";
                    $parts_type = "";
                } else {
                    $parts_type = (int)$attr_id ?? "";
                }
            }
            $parts_material = $goods[51] ?? "";
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
            $parts_num = $form->formatValue($goods[52], 0) ?? 0;
            $parts_gold_weight = $form->formatValue($goods[53], 0) ?? 0;
            $parts_price = $form->formatValue($goods[54], 0) ?? 0;
            //$peishi_num = $form->formatValue($goods[57], 0) ?? 0;
            $peishi_weight = $form->formatValue($goods[55], 0) ?? 0;
            $peishi_gong_fee = $form->formatValue($goods[56], 0) ?? 0;
            $parts_fee = $form->formatValue($goods[57], 0) ?? 0;
            $gong_fee = $form->formatValue($goods[58], 0) ?? 0;
            $xiangqian_craft = $goods[59] ?? "";
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
            $xianqian_price = $form->formatValue($goods[60], 0) ?? 0;
            $biaomiangongyi = $goods[61] ?? "";
            if (!empty($biaomiangongyi)) {
                $attr_id = $form->getAttrIdByAttrValue($style_sn, $biaomiangongyi, AttrIdEnum::FACEWORK);
                if (empty($attr_id)) {
                    $flag = false;
                    $error[$i][] = "表面工艺：[" . $biaomiangongyi . "]录入值有误";
                    $biaomiangongyi = "";
                } else {
                    $biaomiangongyi = $attr_id;
                }
            }
            $biaomiangongyi_fee = $form->formatValue($goods[62], 0) ?? 0;
            $fense_fee = $form->formatValue($goods[63], 0) ?? 0;
            $penlasha_fee = $form->formatValue($goods[64], 0) ?? 0;
            $bukou_fee = $form->formatValue($goods[65], 0) ?? 0;
            $templet_fee = $form->formatValue($goods[66], 0) ?? 0;
            $cert_fee = $form->formatValue($goods[67], 0) ?? 0;
            $other_fee = $form->formatValue($goods[68], 0) ?? 0;
            $main_cert_id = $goods[69] ?? "";
            if (empty($main_cert_id)) {
                $main_cert_id = $cert_id;
            }
            $main_cert_type = $goods[70] ?? "";
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
            $markup_rate = $form->formatValue($goods[71], 1) ?? 1;
            $jintuo_type = $goods[72] ?? "";
            if (!empty($jintuo_type)) {
                $jintuo_type = JintuoTypeEnum::getIdByName($jintuo_type);
                if (empty($jintuo_type)) {
                    $flag = false;
                    $error[$i][] = "金托类型：[" . $jintuo_type . "]录入值有误";
                    $jintuo_type = "";
                }
            }
            $remark = $goods[73] ?? "";
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
                'put_in_type' => $bill->put_in_type,
                'qiban_sn' => $qiban_sn,
                'qiban_type' => $qiban_type,
                'goods_name' => $goods_name,
                'goods_num' => $goods_num,
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
//                'main_stone_size' => $main_stone_size,
                'second_pei_type' => $second_pei_type,
                'second_stone_sn1' => $second_stone_sn1,
                'second_stone_type1' => $second_stone_type1,
                'second_stone_num1' => $second_stone_num1,
                'second_stone_weight1' => $second_stone_weight1,
                'second_stone_price1' => $second_stone_price1,
                'second_stone_shape1' => $second_stone_shape1,
                'second_stone_color1' => $second_stone_color1,
                'second_stone_clarity1' => $second_stone_clarity1,
                'second_stone_cut1' => $second_stone_cut1,
                'second_stone_colour1' => $second_stone_colour1,
                'second_pei_type2' => $second_pei_type2,
                'second_stone_sn2' => $second_stone_sn2,
                'second_stone_type2' => $second_stone_type2,
                'second_stone_num2' => $second_stone_num2,
                'second_stone_weight2' => $second_stone_weight2,
                'second_stone_price2' => $second_stone_price2,
//                'second_stone_shape2' => $second_stone_shape2,
//                'second_stone_size2' => $second_stone_size2,
                'stone_remark' => $stone_remark,
                'parts_way' => $parts_way,
                'parts_type' => $parts_type,
                'parts_material' => $parts_material,
                'parts_num' => $parts_num,
                'parts_gold_weight' => $parts_gold_weight,
                'parts_price' => $parts_price,
//                'peishi_num' => $peishi_num,
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
                $s = "【" . implode('】,【', $v) . '】';
                $message .= '第' . ($k + 1) . '行：' . $s . '<hr>';
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

        //同步更新价格
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
     * 配石费=((副石重/数量)小于0.03ct的，*数量*配石工费)[配石费=(配石重量*配石工费/ct)]
     * @param WarehouseBillTGoodsForm $form
     * @return integer
     * @throws
     */
    public function calculatePeishiFee($form)
    {
        return bcmul($form->peishi_weight, $form->peishi_gong_fee) ?? 0;
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