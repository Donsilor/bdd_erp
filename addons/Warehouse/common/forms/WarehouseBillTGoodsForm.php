<?php

namespace addons\Warehouse\common\forms;

use addons\Warehouse\common\models\WarehouseBillGoodsL;
use addons\Warehouse\common\enums\PeiJianWayEnum;
use addons\Warehouse\common\enums\PeiLiaoWayEnum;
use addons\Warehouse\common\enums\PeiShiWayEnum;
use addons\Style\common\enums\AttrIdEnum;
use common\helpers\StringHelper;
use common\helpers\ArrayHelper;

/**
 * 其它收货单明细 Form
 *
 */
class WarehouseBillTGoodsForm extends WarehouseBillGoodsL
{
    public $ids;
    public $file;
    public $batch_name;
    public $batch_value;
    public $attr_id;
    public $attr_list;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['goods_sn', 'to_warehouse_id', 'is_wholesale', 'auto_goods_id', 'goods_num'], 'required'],
            [['file'], 'file', 'extensions' => ['csv']],//'skipOnEmpty' => false,
            [['ids', 'batch_name', 'batch_value', 'attr_id', 'attr_list'], 'safe'],
        ];
        return array_merge(parent::rules(), $rules);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        //合并
        return ArrayHelper::merge(parent::attributeLabels(), [
            'is_wholesale' => '是否批发',
            'file' => '文件上传',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getIds()
    {
        if ($this->ids) {
            return StringHelper::explode($this->ids);
        }
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getCopyGoodsIds($goods)
    {
        $goods_ids = "";
        if ($goods) {
            foreach ($goods as $good) {
                $goods_ids .= $good->goods_id . ",";
            }
            $goods_ids = rtrim($goods_ids, ",");
        }
        return $goods_ids ?? "";
    }

    /**
     * {@inheritdoc}
     */
    public function trimField($data)
    {
        $res = [];
        $fieldName = $this->getFieldName();
        foreach ($data as $k => $v) {
            $name = $fieldName[$k] ?? "";
            if ($v !== "") {
                $str = StringHelper::strIconv($v);
                $str = str_replace(',', '，', $str);
                $str = str_replace('】', '', $str);
                $res[$name] = $str;
            } else {
                $res[$name] = "";
            }
        }
        return $res ?? [];
    }

    /**
     * {@inheritdoc}
     */
    public function formatValue($value = null, $defaultValue = null)
    {
        if (!empty($value)) {
            return $value;
        } else {
            return $defaultValue;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function formatTitle($values, $str = "")
    {
        $title = "";
        if (!empty($values)) {
            $title = implode($str . '】', $values) . "】";
        }
        return $title ?? "";
    }

    /**
     * {@inheritdoc}
     */
    public function getFooterValues($name = null, $total = [], $defaultValue = 0)
    {
        $value = $total[$name] ?? $defaultValue;
        $footer_value = $this->getAttributeLabel($name) . "<span style='font-size:16px; color: red;'>[$value]</span>";
        return $footer_value;
    }

    /**
     * {@inheritdoc}
     */
    public function goodsSummary($bill_id = null, $params = null)
    {
        $total = [
            'goods_num' => 0,
            'suttle_weight' => 0,
            'gold_weight' => 0,
            'pure_gold' => 0,
            'lncl_loss_weight' => 0,
            'gold_amount' => 0,
            'main_stone_num' => 0,
            'main_stone_weight' => 0,
            'main_stone_amount' => 0,
            'second_stone_num1' => 0,
            'second_stone_weight1' => 0,
            'second_stone_amount1' => 0,
            'second_stone_num2' => 0,
            'second_stone_weight2' => 0,
            'second_stone_amount2' => 0,
            'second_stone_num3' => 0,
            'second_stone_weight3' => 0,
            'second_stone_amount3' => 0,
            'peishi_weight' => 0,
            'peishi_fee' => 0,
            'parts_num' => 0,
            'parts_gold_weight' => 0,
            'parts_amount' => 0,
            'parts_fee' => 0,
            'basic_gong_fee' => 0,
            'piece_fee' => 0,
            'xianqian_fee' => 0,
            'biaomiangongyi_fee' => 0,
            'fense_fee' => 0,
            'penlasha_fee' => 0,
            'lasha_fee' => 0,
            'bukou_fee' => 0,
            'templet_fee' => 0,
            'tax_amount' => 0,
            'cert_fee' => 0,
            'other_fee' => 0,
            'factory_cost' => 0,
            'cost_price' => 0,
            //'unit_cost_price' => 0,
            'cost_amount' => 0,
            'market_price' => 0,
        ];
        $goods = $this->find()->select(array_keys($total))->where(['bill_id' => $bill_id])->all();
        if (!empty($goods)) {
            foreach ($goods as $good) {
                $total['goods_num'] = bcadd($total['goods_num'], $good->goods_num);
                $total['suttle_weight'] = bcadd($total['suttle_weight'], $good->suttle_weight, 3);
                $total['gold_weight'] = bcadd($total['gold_weight'], $good->gold_weight, 3);
                $total['pure_gold'] = bcadd($total['pure_gold'], $good->pure_gold, 3);
                $total['lncl_loss_weight'] = bcadd($total['lncl_loss_weight'], $good->lncl_loss_weight, 3);
                $total['gold_amount'] = bcadd($total['gold_amount'], $good->gold_amount, 3);
                $total['main_stone_num'] = bcadd($total['main_stone_num'], $good->main_stone_num);
                $total['main_stone_weight'] = bcadd($total['main_stone_weight'], $good->main_stone_weight, 3);
                $total['main_stone_amount'] = bcadd($total['main_stone_amount'], $good->main_stone_amount, 3);
                $total['second_stone_num1'] = bcadd($total['second_stone_num1'], $good->second_stone_num1);
                $total['second_stone_weight1'] = bcadd($total['second_stone_weight1'], $good->second_stone_weight1, 3);
                $total['second_stone_amount1'] = bcadd($total['second_stone_amount1'], $good->second_stone_amount1, 3);
                $total['second_stone_num2'] = bcadd($total['second_stone_num2'], $good->second_stone_num2);
                $total['second_stone_weight2'] = bcadd($total['second_stone_weight2'], $good->second_stone_weight2, 3);
                $total['second_stone_amount2'] = bcadd($total['second_stone_amount2'], $good->second_stone_amount2, 3);
                $total['second_stone_num3'] = bcadd($total['second_stone_num3'], $good->second_stone_num3);
                $total['second_stone_weight3'] = bcadd($total['second_stone_weight3'], $good->second_stone_weight3, 3);
                $total['second_stone_amount3'] = bcadd($total['second_stone_amount3'], $good->second_stone_amount3, 3);
                $total['peishi_weight'] = bcadd($total['peishi_weight'], $good->peishi_weight, 3);
                $total['peishi_fee'] = bcadd($total['peishi_fee'], $good->peishi_fee, 3);
                $total['parts_num'] = bcadd($total['parts_num'], $good->parts_num);
                $total['parts_gold_weight'] = bcadd($total['parts_gold_weight'], $good->parts_gold_weight, 3);
                $total['parts_amount'] = bcadd($total['parts_amount'], $good->parts_amount, 3);
                $total['parts_fee'] = bcadd($total['parts_fee'], $good->parts_fee, 3);
                $total['piece_fee'] = bcadd($total['piece_fee'], $good->piece_fee, 3);
                $total['basic_gong_fee'] = bcadd($total['basic_gong_fee'], $good->basic_gong_fee, 3);
                $total['xianqian_fee'] = bcadd($total['xianqian_fee'], $good->xianqian_fee, 3);
                $total['biaomiangongyi_fee'] = bcadd($total['biaomiangongyi_fee'], $good->biaomiangongyi_fee, 3);
                $total['fense_fee'] = bcadd($total['fense_fee'], $good->fense_fee, 3);
                $total['penlasha_fee'] = bcadd($total['penlasha_fee'], $good->penlasha_fee, 3);
                $total['lasha_fee'] = bcadd($total['lasha_fee'], $good->lasha_fee, 3);
                $total['bukou_fee'] = bcadd($total['bukou_fee'], $good->bukou_fee, 3);
                $total['templet_fee'] = bcadd($total['templet_fee'], $good->templet_fee, 3);
                $total['tax_amount'] = bcadd($total['tax_amount'], $good->tax_amount, 3);
                $total['cert_fee'] = bcadd($total['cert_fee'], $good->cert_fee, 3);
                $total['other_fee'] = bcadd($total['other_fee'], $good->other_fee, 3);
                $total['factory_cost'] = bcadd($total['factory_cost'], $good->factory_cost, 3);
                $total['cost_price'] = bcadd($total['cost_price'], $good->cost_price, 3);
                //$total['unit_cost_price'] = bcadd($total['unit_cost_price'], $good->unit_cost_price, 3);
                $total['cost_amount'] = bcadd($total['cost_amount'], $good->cost_amount, 3);
                //标签价
                $total['market_price'] = bcadd($total['market_price'], $good->market_price, 3);
            }
        }
        return $total;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitleList()
    {
        $values = [
            '条码号(货号)为空则系统自动生成',
            '[非起版]和[有款起版]款号不能为空',
            '#',
            $this->formatTitle($this->getJietuoTypeMap()),//'金托类型'
            '[起版号]和[款号]必填其一',
            $this->formatTitle($this->getWarehouseMap()),//'入库仓库'
            $this->formatTitle($this->getMaterialTypeMap()),//'材质'
            $this->formatTitle($this->getMaterialColorMap()),//'材质颜色'
            '不填默认为1',
            $this->formatTitle($this->getFingerHkMap()),//'手寸(港号)'
            $this->formatTitle($this->getFingerMap()),//'手寸(美号)'
            '#', '#',
            $this->formatTitle($this->getXiangkouMap()),//'镶口'
            '#',
            $this->formatTitle($this->getChainTypeMap()),//'链类型'
            $this->formatTitle($this->getCrampRingMap()),//'扣环'
            $this->formatTitle($this->getTalonHeadTypeMap()),//'爪头形状'

            $this->formatTitle($this->getPeiLiaoWayMap()),//'配料方式'
            '#', '#',
            '填写则不自动计算',//含耗重
            '#',
            '填写则不自动计算',//金料额
            '#',

            $this->formatTitle($this->getPeiShiWayMap()),//'主石配石方式'
            '#',
            $this->formatTitle($this->getMainStoneTypeMap()),//'主石类型'
            '#', '#', '#',
            '填写则不自动计算',//主石成本
            $this->formatTitle($this->getMainStoneShapeMap()),//'主石形状'
            $this->formatTitle($this->getMainStoneColorMap()),//'主石颜色'
            $this->formatTitle($this->getMainStoneClarityMap()),//'主石净度'
            $this->formatTitle($this->getMainStoneCutMap()),//'主石切工'
            $this->formatTitle($this->getMainStonePolishMap()),//'主石抛光'
            $this->formatTitle($this->getMainStoneSymmetryMap()),//'主石对称'
            $this->formatTitle($this->getMainStoneFluorescenceMap()),//'主石荧光'
            $this->formatTitle($this->getMainStoneColourMap()),//'主石色彩'

            $this->formatTitle($this->getPeiShiWayMap()),//'副石1配石方式'
            $this->formatTitle($this->getSecondStoneType1Map()),//'副石1类型'
            '#',
            '#', '#', '#',
            '填写则不自动计算',//副石1成本
            $this->formatTitle($this->getSecondStoneShape1Map()),//'副石1形状'
            $this->formatTitle($this->getSecondStoneColor1Map()),//'副石1颜色'
            $this->formatTitle($this->getSecondStoneClarity1Map()),//'副石1净度'
            $this->formatTitle($this->getSecondStoneCut1Map()),//'副石1切工'
            $this->formatTitle($this->getSecondStoneColour1Map()),//'副石1色彩'

            $this->formatTitle($this->getPeiShiWayMap()),//'副石2配石方式'
            $this->formatTitle($this->getSecondStoneType2Map()),//'副石2类型'
            '#',
            '#', '#', '#',
            '填写则不自动计算',//副石2成本
            $this->formatTitle($this->getSecondStoneColor2Map()),//'副石2颜色'
            $this->formatTitle($this->getSecondStoneClarity2Map()),//'副石2净度'

            $this->formatTitle($this->getPeiShiWayMap()),//'副石3配石方式'
            $this->formatTitle($this->getSecondStoneType3Map()),//'副石3类型'
            '#',
            '#', '#', '#',
            '填写则不自动计算',//副石3成本
            $this->formatTitle($this->getSecondStoneColor3Map()),//'副石3颜色'
            $this->formatTitle($this->getSecondStoneClarity3Map()),//'副石3净度'
            '#',

            $this->formatTitle($this->getPeiJianWayMap()),//'配件方式'
            $this->formatTitle($this->getPartsTypeMap()),//'配件类型'
            $this->formatTitle($this->getPartsMaterialMap()),//'配件材质'
            '#', '#', '#',
            '填写则不自动计算',//配件额

            '#', '#',
            '填写则不自动计算',//配石费
            '#', '#', '#',
            $this->formatTitle($this->getXiangqianCraftMap()),//'镶嵌工艺'
            '#', '#', '#',
            '填写则不自动计算',//镶石费
            $this->formatTitle($this->getFaceCraftMap(), "|"),//'表面工艺'
            '#', '#', '#', '#', '#', '#', '#',
            '填写则不自动计算',//税额
            '#', '#', '#',
            $this->formatTitle($this->getCertTypeMap()),//'主石证书类型'
            '填写则不自动计算',//工厂总成本
            '填写则不自动计算',//公司成本价
            '#', '#',
        ];
        $fields = [
            '条码号(货号)', '(*)款号', '商品名称', '(*)金托类型', '起版号', '(*)入库仓库', '材质', '材质颜色', '货品数量', '手寸(港号)', '手寸(美号)', '尺寸(cm)', '成品尺寸(mm)', '镶口(ct)', '刻字', '链类型', '扣环', '爪头形状',
            '配料方式', '连石重(g)', '损耗(%)', '含耗重(g)', '金价/g', '金料额', '折足率(%)',
            '主石配石方式', '主石编号', '主石类型', '主石粒数', '主石重(ct)', '主石单价/ct', '主石成本', '主石形状', '主石颜色', '主石净度', '主石切工', '主石抛光', '主石对称', '主石荧光', '主石色彩',
            '副石1配石方式', '副石1类型', '副石1编号', '副石1粒数', '副石1重(ct)', '副石1单价/ct', '副石1成本', '副石1形状', '副石1颜色', '副石1净度', '副石1切工', '副石1色彩',
            '副石2配石方式', '副石2类型', '副石2编号', '副石2粒数', '副石2重(ct)', '副石2单价/ct', '副石2成本', '副石2颜色', '副石2净度',
            '副石3配石方式', '副石3类型', '副石3编号', '副石3粒数', '副石3重(ct)', '副石3单价/ct', '副石3成本', '副石3颜色', '副石3净度', '石料备注',
            '配件方式', '配件类型', '配件材质', '配件数量', '配件金重(g)', '配件金价/g', '配件额',
            '配石重量(ct)', '配石工费/ct', '配石费', '配件工费', '克/工费', '件/工费', '镶嵌工艺', '镶石1工费/颗', '镶石2工费/颗', '镶石3工费/颗', '镶石费', '表面工艺(多个用“|”分割)', '表面工艺费', '分色/分件费', '喷沙费', '拉沙费', '补口费', '版费', '税费', '税额', '证书费', '其它费用',
            '主石证书号', '主石证书类型', '工厂总成本', '公司成本总额', '倍率(默认1)', '备注',
        ];
        return [$values, $fields];
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldName()
    {
        $fieldName = [
            'goods_id', 'style_sn', 'goods_name', 'jintuo_type', 'qiban_sn', 'to_warehouse_id', 'material_type', 'material_color', 'goods_num', 'finger_hk', 'finger', 'length', 'product_size', 'xiangkou', 'kezi', 'chain_type', 'cramp_ring', 'talon_head_type',
            'peiliao_way', 'suttle_weight', 'gold_loss', 'lncl_loss_weight', 'gold_price', 'gold_amount', 'pure_gold_rate',
            'main_pei_type', 'main_stone_sn', 'main_stone_type', 'main_stone_num', 'main_stone_weight', 'main_stone_price', 'main_stone_amount', 'main_stone_shape', 'main_stone_color', 'main_stone_clarity', 'main_stone_cut', 'main_stone_polish', 'main_stone_symmetry', 'main_stone_fluorescence', 'main_stone_colour',
            'second_pei_type', 'second_stone_type1', 'second_stone_sn1', 'second_stone_num1', 'second_stone_weight1', 'second_stone_price1', 'second_stone_amount1', 'second_stone_shape1', 'second_stone_color1', 'second_stone_clarity1', 'second_stone_cut1', 'second_stone_colour1',
            'second_pei_type2', 'second_stone_type2', 'second_stone_sn2', 'second_stone_num2', 'second_stone_weight2', 'second_stone_price2', 'second_stone_amount2', 'second_stone_color2', 'second_stone_clarity2',
            'second_pei_type3', 'second_stone_type3', 'second_stone_sn3', 'second_stone_num3', 'second_stone_weight3', 'second_stone_price3', 'second_stone_amount3', 'second_stone_color3', 'second_stone_clarity3', 'stone_remark',
            'parts_way', 'parts_type', 'parts_material', 'parts_num', 'parts_gold_weight', 'parts_price', 'parts_amount',
            'peishi_weight', 'peishi_gong_fee', 'peishi_fee', 'parts_fee', 'gong_fee', 'piece_fee', 'xiangqian_craft', 'second_stone_fee1', 'second_stone_fee2', 'second_stone_fee3', 'xianqian_fee', 'biaomiangongyi', 'biaomiangongyi_fee', 'fense_fee', 'penlasha_fee', 'lasha_fee', 'bukou_fee', 'templet_fee', 'tax_fee', 'tax_amount', 'cert_fee', 'other_fee',
            'main_cert_id', 'main_cert_type', 'factory_cost', 'cost_amount', 'markup_rate', 'remark',
        ];
        return $fieldName ?? [];
    }

    /**
     * {@inheritdoc}
     */
    public function getPeiType($stone_sn = null, $stone_num = null, $stone_weight = null)
    {
        if (!empty($stone_sn)) {
            $pei_type = \addons\Warehouse\common\enums\PeiShiWayEnum::COMPANY;
        } else {
            if (!empty($stone_num) || !empty($stone_weight)) {
                $pei_type = \addons\Warehouse\common\enums\PeiShiWayEnum::FACTORY;
            } else {
                $pei_type = \addons\Warehouse\common\enums\PeiShiWayEnum::NO_PEI;
            }
        }
        return $pei_type;
    }

    /**
     * {@inheritdoc}
     */
    public function getWarehouseId($to_warehouse_id, $warehouseAll = [], $defaultValue = 0)
    {
        if ($warehouseAll) {
            $warehouseAll = array_flip($warehouseAll);
            $warehouse_id = $warehouseAll[$to_warehouse_id] ?? 0;
        }
        return $warehouse_id ?? $defaultValue;
    }

    /**
     * 属性值转换属性ID
     * @param string $style_sn 款号
     * @param string $value 属性值
     * @param int $attr_id 属性ID
     * @return int
     */
    public function getAttrIdByAttrValue($style_sn, $value, $attr_id)
    {
//        if (!empty($style_sn)) {
//            $valueList = $this->getAttrValueListByStyle($style_sn, $attr_id);
//        } else {
        $valueList = \Yii::$app->attr->valueMap($attr_id);
//        }
        $valueList = array_flip($valueList);
        $attrId = isset($valueList[$value]) ? $valueList[$value] : "";
        return (string)$attrId ?? "";
    }

    /**
     * 根据款号获取属性值列表
     * @param string $style_sn
     * @param integer $attr_id
     * @return array
     */
    public function getAttrValueListByStyle($style_sn, $attr_id)
    {
        if($style_sn){
            return \Yii::$app->styleService->styleAttribute->getAttrValueListByStyle($style_sn, $attr_id) ?? [];
        }
        return \Yii::$app->attr->valueMap($attr_id) ?? [];
    }

    /**
     * 款式性别
     * @return array
     */
    public function getStyleSexMap()
    {
        return \addons\Style\common\enums\StyleSexEnum::getMap() ?? [];
    }

    /**
     * 金托类型
     * @return array
     */
    public function getJietuoTypeMap()
    {
        return \addons\Style\common\enums\JintuoTypeEnum::getMap() ?? [];
    }

    /**
     * 是否镶嵌
     * @return array
     */
    public function getIsInlayMap()
    {
        return \addons\Style\common\enums\InlayEnum::getMap() ?? [];
    }

    /**
     * 结算状态
     * @return array
     */
    public function getPayStatusMap()
    {
        return \addons\Warehouse\common\enums\IsSettleAccountsEnum::getMap() ?? [];
    }

    /**
     * 配料方式
     * @return array
     */
    public function getPeiLiaoWayMap()
    {
        return \addons\Warehouse\common\enums\PeiLiaoWayEnum::getMap() ?? [];
    }

    /**
     * 配石方式(类型)
     * @return array
     */
    public function getPeiShiWayMap()
    {
        return \addons\Warehouse\common\enums\PeiShiWayEnum::getMap() ?? [];
    }

    /**
     * 配件方式
     * @return array
     */
    public function getPeiJianWayMap()
    {
        return \addons\Warehouse\common\enums\PeiJianWayEnum::getMap() ?? [];
    }

    /**
     * 入库仓库列表
     * @return array
     */
    public function getWarehouseMap()
    {
        return \Yii::$app->warehouseService->warehouse::getDropDown() ?? [];
    }

    /**
     * 款式分类列表
     * @return array
     */
    public function getCateMap()
    {
        return \Yii::$app->styleService->styleCate::getDropDown() ?? [];
    }

    /**
     * 产品线列表
     * @return array
     */
    public function getProductMap()
    {
        return \Yii::$app->styleService->productType::getDropDown() ?? [];
    }

    /**
     * 起版类型
     * @return array
     */
    public function getQibanTypeMap()
    {
        return \addons\Style\common\enums\QibanTypeEnum::getMap() ?? [];
    }

    /**
     * 材质列表
     * @return array
     */
    public function getMaterialTypeMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::MATERIAL_TYPE) ?? [];
    }

    /**
     * 材质
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getMaterialTypeDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::MATERIAL_TYPE);
        } else {
            $data = $this->getMaterialTypeMap();
        }
        return $data ?? [];
    }

    /**
     * 材质颜色列表
     * @return array
     */
    public function getMaterialColorMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::MATERIAL_COLOR) ?? [];
    }

    /**
     * 材质颜色
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getMaterialColorDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::MATERIAL_COLOR);
        } else {
            $data = $this->getMaterialColorMap();
        }
        return $data ?? [];
    }

    /**
     * 港号列表
     * @return array
     */
    public function getFingerHkMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::FINGER_HK) ?? [];
    }

    /**
     * 港号
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getFingerHkDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::FINGER_HK);
        } else {
            $data = $this->getFingerHkMap();
        }
        return $data ?? [];
    }

    /**
     * 美号列表
     * @return array
     */
    public function getFingerMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::FINGER) ?? [];
    }

    /**
     * 美号
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getFingerDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::FINGER);
        } else {
            $data = $this->getFingerMap();
        }
        return $data ?? [];
    }

    /**
     * 镶口列表
     * @return array
     */
    public function getXiangkouMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::XIANGKOU) ?? [];
    }

    /**
     * 镶口
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getXiangkouDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::XIANGKOU);
        } else {
            $data = $this->getXiangkouMap();
        }
        return $data ?? [];
    }

    /**
     * 链类型列表
     * @return array
     */
    public function getChainTypeMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::CHAIN_TYPE) ?? [];
    }

    /**
     * 链类型
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getChainTypeDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::CHAIN_TYPE);
        } else {
            $data = $this->getChainTypeMap();
        }
        return $data ?? [];
    }

    /**
     * 链扣环列表
     * @return array
     */
    public function getCrampRingMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::CHAIN_BUCKLE) ?? [];
    }

    /**
     * 链扣环
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getCrampRingDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::CHAIN_BUCKLE);
        } else {
            $data = $this->getCrampRingMap();
        }
        return $data ?? [];
    }

    /**
     * 爪头形状列表
     * @return array
     */
    public function getTalonHeadTypeMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::TALON_HEAD_TYPE) ?? [];
    }

    /**
     * 爪头形状
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getTalonHeadTypeDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::TALON_HEAD_TYPE);
        } else {
            $data = $this->getTalonHeadTypeMap();
        }
        return $data ?? [];
    }

    /**
     * 证书类型列表
     * @return array
     */
    public function getCertTypeMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::DIA_CERT_TYPE) ?? [];
    }

    /**
     * 证书类型
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getCertTypeDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::DIA_CERT_TYPE);
        } else {
            $data = $this->getCertTypeMap();
        }
        return $data ?? [];
    }

    /**
     * 钻石证书类型列表
     * @return array
     */
    public function getDiamondCertTypeMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::DIA_CERT_TYPE) ?? [];
    }

    /**
     * 钻石证书类型
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getDiamondCertTypeDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::DIA_CERT_TYPE);
        } else {
            $data = $this->getDiamondCertTypeMap();
        }
        return $data ?? [];
    }

    /**
     * 钻石颜色列表
     * @return array
     */
    public function getDiamondColorMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::DIA_COLOR) ?? [];
    }

    /**
     * 钻石颜色
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getDiamondColorDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::DIA_COLOR);
        } else {
            $data = $this->getDiamondColorMap();
        }
        return $data ?? [];
    }

    /**
     * 钻石形状列表
     * @return array
     */
    public function getDiamondShapeMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::DIA_SHAPE) ?? [];
    }

    /**
     * 钻石形状
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getDiamondShapeDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::DIA_SHAPE);
        } else {
            $data = $this->getDiamondShapeMap();
        }
        return $data ?? [];
    }

    /**
     * 钻石净度列表
     * @return array
     */
    public function getDiamondClarityMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::DIA_CLARITY) ?? [];
    }

    /**
     * 钻石净度
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getDiamondClarityDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::DIA_CLARITY);
        } else {
            $data = $this->getDiamondClarityMap();
        }
        return $data ?? [];
    }

    /**
     * 钻石切工列表
     * @return array
     */
    public function getDiamondCutMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::DIA_CUT) ?? [];
    }

    /**
     * 钻石切工
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getDiamondCutDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::DIA_CUT);
        } else {
            $data = $this->getDiamondCutMap();
        }
        return $data ?? [];
    }

    /**
     * 钻石抛光列表
     * @return array
     */
    public function getDiamondPolishMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::DIA_POLISH) ?? [];
    }

    /**
     * 钻石抛光
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getDiamondPolishDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::DIA_POLISH);
        } else {
            $data = $this->getDiamondPolishMap();
        }
        return $data ?? [];
    }

    /**
     * 钻石对称列表
     * @return array
     */
    public function getDiamondSymmetryMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::DIA_SYMMETRY) ?? [];
    }

    /**
     * 钻石对称
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getDiamondSymmetryDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::DIA_SYMMETRY);
        } else {
            $data = $this->getDiamondSymmetryMap();
        }
        return $data ?? [];
    }

    /**
     * 钻石荧光列表
     * @return array
     */
    public function getDiamondFluorescenceMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::DIA_FLUORESCENCE) ?? [];
    }

    /**
     * 钻石荧光
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getDiamondFluorescenceDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::DIA_FLUORESCENCE);
        } else {
            $data = $this->getDiamondFluorescenceMap();
        }
        return $data ?? [];
    }

    /**
     * 主石类型列表
     * @return array
     */
    public function getMainStoneTypeMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_TYPE) ?? [];
    }

    /**
     * 主石类型
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getMainStoneTypeDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::MAIN_STONE_TYPE);
        } else {
            $data = $this->getMainStoneTypeMap();
        }
        return $data ?? [];
    }

    /**
     * 主石证书类型列表
     * @return array
     */
    public function getMainCertTypeMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::DIA_CERT_TYPE) ?? [];
    }

    /**
     * 主石证书类型
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getMainCertTypeDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::DIA_CERT_TYPE);
        } else {
            $data = $this->getMainCertTypeMap();
        }
        return $data ?? [];
    }

    /**
     * 主石颜色列表
     * @return array
     */
    public function getMainStoneColorMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_COLOR) ?? [];
    }

    /**
     * 主石颜色
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getMainStoneColorDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::MAIN_STONE_COLOR);
        } else {
            $data = $this->getMainStoneColorMap();
        }
        return $data ?? [];
    }

    /**
     * 主石形状列表
     * @return array
     */
    public function getMainStoneShapeMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_SHAPE) ?? [];
    }

    /**
     * 主石形状
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getMainStoneShapeDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::MAIN_STONE_SHAPE);
        } else {
            $data = $this->getMainStoneShapeMap();
        }
        return $data ?? [];
    }

    /**
     * 主石净度列表
     * @return array
     */
    public function getMainStoneClarityMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_CLARITY) ?? [];
    }

    /**
     * 主石净度
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getMainStoneClarityDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::MAIN_STONE_CLARITY);
        } else {
            $data = $this->getMainStoneClarityMap();
        }
        return $data ?? [];
    }

    /**
     * 主石切工列表
     * @return array
     */
    public function getMainStoneCutMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_CUT) ?? [];
    }

    /**
     * 主石切工
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getMainStoneCutDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::MAIN_STONE_CUT);
        } else {
            $data = $this->getMainStoneCutMap();
        }
        return $data ?? [];
    }

    /**
     * 主石抛光列表
     * @return array
     */
    public function getMainStonePolishMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_POLISH) ?? [];
    }

    /**
     * 主石抛光
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getMainStonePolishDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::MAIN_STONE_POLISH);
        } else {
            $data = $this->getMainStonePolishMap();
        }
        return $data ?? [];
    }

    /**
     * 主石对称列表
     * @return array
     */
    public function getMainStoneSymmetryMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_SYMMETRY) ?? [];
    }

    /**
     * 主石对称
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getMainStoneSymmetryDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::MAIN_STONE_SYMMETRY);
        } else {
            $data = $this->getMainStoneSymmetryMap();
        }
        return $data ?? [];
    }

    /**
     * 主石荧光列表
     * @return array
     */
    public function getMainStoneFluorescenceMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_FLUORESCENCE) ?? [];
    }

    /**
     * 主石荧光
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getMainStoneFluorescenceDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::MAIN_STONE_FLUORESCENCE);
        } else {
            $data = $this->getMainStoneFluorescenceMap();
        }
        return $data ?? [];
    }

    /**
     * 主石色彩列表
     * @return array
     */
    public function getMainStoneColourMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::MAIN_STONE_COLOUR) ?? [];
    }

    /**
     * 主石色彩
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getMainStoneColourDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::MAIN_STONE_COLOUR);
        } else {
            $data = $this->getMainStoneColourMap();
        }
        return $data ?? [];
    }

    /**
     * 副石1类型列表
     * @return array
     */
    public function getSecondStoneType1Map()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_TYPE) ?? [];
    }

    /**
     * 副石1类型
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getSecondStoneType1Drop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::SIDE_STONE1_TYPE);
        } else {
            $data = $this->getSecondStoneType1Map();
        }
        return $data ?? [];
    }

    /**
     * 副石1形状列表
     * @return array
     */
    public function getSecondStoneShape1Map()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_SHAPE) ?? [];
    }

    /**
     * 副石1形状
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getSecondStoneShape1Drop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::SIDE_STONE1_SHAPE);
        } else {
            $data = $this->getSecondStoneShape1Map();
        }
        return $data ?? [];
    }

    /**
     * 副石1颜色列表
     * @return array
     */
    public function getSecondStoneColor1Map()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_COLOR) ?? [];
    }

    /**
     * 副石1颜色
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getSecondStoneColor1Drop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::SIDE_STONE1_COLOR);
        } else {
            $data = $this->getSecondStoneColor1Map();
        }
        return $data ?? [];
    }

    /**
     * 副石1净度列表
     * @return array
     */
    public function getSecondStoneClarity1Map()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_CLARITY) ?? [];
    }

    /**
     * 副石1净度
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getSecondStoneClarity1Drop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::SIDE_STONE1_CLARITY);
        } else {
            $data = $this->getSecondStoneClarity1Map();
        }
        return $data ?? [];
    }

    /**
     * 副石1切工列表
     * @return array
     */
    public function getSecondStoneCut1Map()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_CUT) ?? [];
    }

    /**
     * 副石1切工
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getSecondStoneCut1Drop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::SIDE_STONE1_CUT);
        } else {
            $data = $this->getSecondStoneCut1Map();
        }
        return $data ?? [];
    }

    /**
     * 副石1色彩列表
     * @return array
     */
    public function getSecondStoneColour1Map()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE1_COLOUR) ?? [];
    }

    /**
     * 副石1色彩
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getSecondStoneColour1Drop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::SIDE_STONE1_COLOUR);
        } else {
            $data = $this->getSecondStoneColour1Map();
        }
        return $data ?? [];
    }

    /**
     * 副石2类型列表
     * @return array
     */
    public function getSecondStoneType2Map()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE2_TYPE) ?? [];
    }

    /**
     * 副石2类型
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getSecondStoneType2Drop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::SIDE_STONE2_TYPE);
        } else {
            $data = $this->getSecondStoneType2Map();
        }
        return $data ?? [];
    }

    /**
     * 副石2形状列表
     * @return array
     */
    public function getSecondStoneShape2Map()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE2_SHAPE) ?? [];
    }

    /**
     * 副石2形状
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getSecondStoneShape2Drop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::SIDE_STONE2_SHAPE);
        } else {
            $data = $this->getSecondStoneShape2Map();
        }
        return $data ?? [];
    }

    /**
     * 副石2颜色列表
     * @return array
     */
    public function getSecondStoneColor2Map()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE2_COLOR) ?? [];
    }

    /**
     * 副石2颜色
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getSecondStoneColor2Drop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::SIDE_STONE2_COLOR);
        } else {
            $data = $this->getSecondStoneColor2Map();
        }
        return $data ?? [];
    }

    /**
     * 副石2净度列表
     * @return array
     */
    public function getSecondStoneClarity2Map()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE2_CLARITY) ?? [];
    }

    /**
     * 副石2净度
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getSecondStoneClarity2Drop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::SIDE_STONE2_CLARITY);
        } else {
            $data = $this->getSecondStoneClarity2Map();
        }
        return $data ?? [];
    }

    /**
     * 副石2色彩列表
     * @return array
     */
    public function getSecondStoneColour2Map()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE2_COLOUR) ?? [];
    }

    /**
     * 副石2色彩
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getSecondStoneColour2Drop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::SIDE_STONE2_COLOUR);
        } else {
            $data = $this->getSecondStoneColour2Map();
        }
        return $data ?? [];
    }

    /**
     * 副石3类型列表
     * @return array
     */
    public function getSecondStoneType3Map()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE3_TYPE) ?? [];
    }

    /**
     * 副石3类型
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getSecondStoneType3Drop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::SIDE_STONE3_TYPE);
        } else {
            $data = $this->getSecondStoneType3Map();
        }
        return $data ?? [];
    }

    /**
     * 副石3颜色列表
     * @return array
     */
    public function getSecondStoneColor3Map()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE3_COLOR) ?? [];
    }

    /**
     * 副石3颜色
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getSecondStoneColor3Drop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::SIDE_STONE3_COLOR);
        } else {
            $data = $this->getSecondStoneColor3Map();
        }
        return $data ?? [];
    }

    /**
     * 副石3净度列表
     * @return array
     */
    public function getSecondStoneClarity3Map()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::SIDE_STONE3_CLARITY) ?? [];
    }

    /**
     * 副石3净度
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getSecondStoneClarity3Drop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::SIDE_STONE3_CLARITY);
        } else {
            $data = $this->getSecondStoneClarity3Map();
        }
        return $data ?? [];
    }

    /**
     * 配件类型列表
     * @return array
     */
    public function getPartsTypeMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::MAT_PARTS_TYPE) ?? [];
    }

    /**
     * 配件类型
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getPartsTypeDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::MAT_PARTS_TYPE);
        } else {
            $data = $this->getPartsTypeMap();
        }
        return $data ?? [];
    }

    /**
     * 配件材质列表
     * @return array
     */
    public function getPartsMaterialMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::MATERIAL_TYPE) ?? [];
    }

    /**
     * 配件材质
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getPartsMaterialDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::MATERIAL_TYPE);
        } else {
            $data = $this->getPartsMaterialMap();
        }
        return $data ?? [];
    }

    /**
     * 镶嵌工艺列表
     * @return array
     */
    public function getXiangqianCraftMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::XIANGQIAN_CRAFT) ?? [];
    }

    /**
     * 镶嵌工艺
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getXiangqianCraftDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::XIANGQIAN_CRAFT);
        } else {
            $data = $this->getXiangqianCraftMap();
        }
        return $data ?? [];
    }

    /**
     * 表面工艺列表
     * @return array
     */
    public function getFaceCraftMap()
    {
        return \Yii::$app->attr->valueMap(AttrIdEnum::FACEWORK) ?? [];
    }

    /**
     * 表面工艺
     * @param WarehouseBillTGoodsForm $form
     * @return array
     */
    public function getFaceCraftDrop($form)
    {
        if (!empty($form->style_sn)) {
            $data = $this->getAttrValueListByStyle($form->style_sn, AttrIdEnum::FACEWORK);
        } else {
            $data = $this->getFaceCraftMap();
        }
        return $data ?? [];
    }

    /**
     * 自定义表单验证
     * {@inheritdoc}
     * @param WarehouseBillTGoodsForm $form
     */
    public function updateFromValidate($form, $is_import = false)
    {
        $result = ['error' => true, 'data' => [], 'msg' => ''];
        //金料
//        if ($form->peiliao_way == PeiLiaoWayEnum::NO_PEI) {
//            if ($form->suttle_weight > 0
//                || $form->lncl_loss_weight > 0
//                || $form->gold_loss > 0
//                || $form->pure_gold_rate > 0
//                || $form->gold_price > 0) {
//                $result['error'] = false;
//                $msg[]= "配料方式为不需配料，金料信息不能填写";
//            }
//        }
        $msg = [];
        if (!$is_import) {
            //主石
            if ($form->main_pei_type == PeiShiWayEnum::NO_PEI) {
                if (
                    $form->main_stone_num
                    || $form->main_stone_weight > 0
//                $form->main_stone_sn
//                || $form->main_stone_type
//                || $form->main_stone_num
//                || $form->main_stone_weight > 0
//                || $form->main_stone_shape
//                || $form->main_stone_color
//                || $form->main_stone_clarity
//                || $form->main_stone_cut
//                || $form->main_stone_colour
//                || $form->main_stone_size
//                || $form->main_cert_id
//                || $form->main_cert_type
//                || $form->main_stone_price > 0
//                || $form->main_stone_amount > 0
                ) {
                    $result['error'] = false;
                    $msg[] = "主石配石方式为不需配石，主石信息不能填写";
                }
            }
            //副石1
            if ($form->second_pei_type == PeiShiWayEnum::NO_PEI) {
                if (
                    $form->second_stone_num1
                    || $form->second_stone_weight1 > 0
//                $form->second_stone_sn1
//                || $form->second_stone_type1
//                || $form->second_stone_num1
//                || $form->second_stone_weight1 > 0
//                || $form->second_stone_shape1
//                || $form->second_stone_color1
//                || $form->second_stone_clarity1
//                || $form->second_stone_cut1
//                || $form->second_stone_colour1
//                || $form->second_stone_size1
//                || $form->second_cert_id1
//                || $form->second_stone_price1 > 0
//                || $form->second_stone_amount1 > 0
                ) {
                    $result['error'] = false;
                    $msg[] = "副石1配石方式为不需配石，副石1信息不能填写";
                }
            }
            //副石2
            if ($form->second_pei_type2 == PeiShiWayEnum::NO_PEI) {
                if (
                    $form->second_stone_num2
                    || $form->second_stone_weight2 > 0
//                $form->second_stone_sn2
//                || $form->second_stone_type2
//                || $form->second_stone_num2
//                || $form->second_stone_weight2 > 0
//                || $form->second_stone_shape2
//                || $form->second_stone_color2
//                || $form->second_stone_clarity2
//                || $form->second_stone_colour2
//                || $form->second_stone_size2
//                || $form->second_cert_id2
//                || $form->second_stone_price2 > 0
//                || $form->second_stone_amount2 > 0
                ) {
                    $result['error'] = false;
                    $msg[] = "副石2配石方式为不需配石，副石2信息不能填写";
                }
            }
            //副石3
            if ($form->second_pei_type3 == PeiShiWayEnum::NO_PEI) {
                if (
                    $form->second_stone_num3
                    || $form->second_stone_weight3 > 0
//                $form->second_stone_sn3
//                || $form->second_stone_type3
//                || $form->second_stone_num3
//                || $form->second_stone_weight3 > 0
//                || $form->second_stone_price3 > 0
//                || $form->second_stone_amount3 > 0
                ) {
                    $result['error'] = false;
                    $msg[] = "副石3配石方式为不需配石，副石3信息不能填写";
                }
            }
            //配件
//        if ($form->parts_way == PeiJianWayEnum::NO_PEI) {
//            if ($form->parts_type
//                || $form->parts_num
//                || $form->parts_material
//                || $form->parts_gold_weight > 0
//                || $form->parts_price > 0
//                || $form->parts_amount > 0) {
//                $result['error'] = false;
//                $msg[]= "配件方式为不需配件，配件信息不能填写";
//            }
//        }
        }
        //主石粒数/商品数量，必须为整数
        $main_stone_num = $form->main_stone_num / $form->goods_num;
        if (!is_int($main_stone_num)) {
            $result['error'] = false;
            $msg[] = "主石粒数[$form->main_stone_num]/商品数量[$form->goods_num]=[$main_stone_num]，必须为整数";
        }
        $second_stone_num1 = $form->second_stone_num1 / $form->goods_num;
        if (!is_int($second_stone_num1)) {
            $result['error'] = false;
            $msg[] = "副石1粒数[$form->second_stone_num1]/商品数量[$form->main_stone_num]=[$second_stone_num1]，必须为整数";
        }
        $second_stone_num2 = $form->second_stone_num2 / $form->goods_num;
        if (!is_int($second_stone_num2)) {
            $result['error'] = false;
            $msg[] = "副石2粒数[$form->second_stone_num2]/商品数量[$form->main_stone_num]=[$second_stone_num2]，必须为整数";
        }
        $second_stone_num3 = $form->second_stone_num3 / $form->goods_num;
        if (!is_int($second_stone_num3)) {
            $result['error'] = false;
            $msg[] = "副石3粒数[$form->second_stone_num3]/商品数量[$form->main_stone_num]=[$second_stone_num3]，必须为整数";
        }
        $parts_num = $form->parts_num / $form->goods_num;
        if (!is_int($parts_num)) {
            $result['error'] = false;
            $msg[] = "配件数量[$form->parts_num]/商品数量[$form->main_stone_num]=[$parts_num]，必须为整数";
        }
        //工费
        if ($form->gong_fee > 0 && $form->piece_fee > 0) {
            $result['error'] = false;
            $msg[] = "克工费与件工费只能填写一个";
        }
        if (!empty($msg)) {
            $result['msg'] = implode('】,【', $msg)."[条码号=".$form->goods_id."]";
        }
        return $result;
    }
}
