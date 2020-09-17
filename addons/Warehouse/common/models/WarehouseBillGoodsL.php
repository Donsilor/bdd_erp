<?php

namespace addons\Warehouse\common\models;

use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use Yii;

/**
 * This is the model class for table "warehouse_bill_goods_l".
 *
 * @property int $id ID
 * @property int $bill_id 单据ID
 * @property string $bill_no 单据编号
 * @property string $bill_type 单据类型
 * @property string $goods_id 货号
 * @property string $goods_name 商品名称
 * @property int $goods_num 商品数量
 * @property string $goods_sn 款号/起版号
 * @property string $goods_image 商品图片
 * @property int $style_id 款式ID
 * @property string $style_sn 款号
 * @property int $style_cate_id 款式分类
 * @property int $product_type_id 产品线
 * @property int $style_sex 款式性别
 * @property int $style_channel_id 款式渠道
 * @property int $qiban_id 起版ID
 * @property string $qiban_sn 起版号
 * @property int $qiban_type 起版类型
 * @property string $order_sn 订单号
 * @property int $order_detail_id 订单明细ID
 * @property int $supplier_id 供应商ID
 * @property int $to_warehouse_id 入库仓库
 * @property int $put_in_type 入库方式
 * @property string $material 主成色
 * @property string $material_type 材质
 * @property string $material_color 材质颜色
 * @property string $xiangkou 镶口(ct)
 * @property string $finger 手寸(美)
 * @property string $finger_hk 手寸(港)
 * @property string $length 长度(cm)
 * @property string $product_size 尺寸(mm)
 * @property string $kezi 刻字
 * @property double $chain_long 链长(mm)
 * @property string $chain_type 链类型
 * @property string $cramp_ring 扣环
 * @property string $talon_head_type 爪头形状
 * @property string $xiangqian_craft 镶嵌工艺
 * @property string $biaomiangongyi 表面工艺
 * @property string $goods_color 货品外部颜色
 * @property string $cert_id 证书号
 * @property string $cert_type 证书类别
 * @property int $jintuo_type 金托类型
 * @property int $peiliao_way 配料方式
 * @property string $gold_weight 金重(g)
 * @property string $suttle_weight 连石重(净重g)
 * @property string $gold_loss 金损(%)
 * @property string $lncl_loss_weight 含耗重(g)
 * @property string $gross_weight 毛重(g)
 * @property string $pure_gold 折足(g)
 * @property string $pure_gold_rate 折足率(%)
 * @property string $gold_price 金价/g
 * @property string $gold_amount 金料额(金料成本)
 * @property string $diamond_carat 钻石大小(ct)
 * @property string $diamond_color 钻石颜色
 * @property string $diamond_shape 钻石形状
 * @property string $diamond_clarity 钻石净度
 * @property string $diamond_cut 钻石切工
 * @property string $diamond_polish 钻石抛光
 * @property string $diamond_symmetry 钻石对称
 * @property string $diamond_fluorescence 钻石荧光
 * @property string $diamond_discount 钻石折扣(%)
 * @property string $diamond_cert_type 钻石证书类型
 * @property string $diamond_cert_id 钻石证书号
 * @property int $main_pei_type 主石配石类型
 * @property string $main_stone_sn 主石编号
 * @property string $main_stone_type 主石类型
 * @property int $main_stone_num 主石粒数
 * @property string $main_stone_weight 主石重(ct)
 * @property string $main_stone_shape 主石形状
 * @property string $main_stone_color 主石颜色
 * @property string $main_stone_clarity 主石净度
 * @property string $main_stone_cut 主石切工
 * @property string $main_stone_colour 主石色彩
 * @property string $main_stone_size 主石规格
 * @property string $main_stone_price 主石单价/ct
 * @property string $main_stone_amount 主石成本(主石额)
 * @property string $main_cert_id 主石证书号
 * @property string $main_cert_type 主石证书类型
 * @property int $second_pei_type 副石配石类型
 * @property string $second_stone_sn1 副石1编号
 * @property string $second_stone_type1 副石1类型
 * @property int $second_stone_num1 副石1粒数
 * @property string $second_stone_weight1 副石1重(ct)
 * @property string $second_stone_shape1 副石1形状
 * @property string $second_stone_color1 副石1颜色
 * @property string $second_stone_clarity1 副石1净度
 * @property string $second_stone_cut1 副石1切工
 * @property string $second_stone_colour1 副石色彩
 * @property string $second_stone_size1 副石1规格
 * @property string $second_stone_price1 副石1单价/ct
 * @property string $second_stone_amount1 副石1成本(副石1额)
 * @property string $second_cert_id1 副石1证书号
 * @property int $second_pei_type2 副石2配石类型
 * @property string $second_stone_sn2 副石2编号
 * @property string $second_stone_type2 副石2类型
 * @property int $second_stone_num2 副石2粒数
 * @property string $second_stone_weight2 副石2重(ct)
 * @property string $second_stone_shape2 副石2形状
 * @property string $second_stone_color2 副石2颜色
 * @property string $second_stone_clarity2 副石2净度
 * @property string $second_stone_colour2 副石2色彩
 * @property string $second_stone_size2 副石2规格
 * @property string $second_stone_price2 副石2单价/ct
 * @property string $second_stone_amount2 副石2成本(副石2额)
 * @property string $second_cert_id2 副石2证书号
 * @property int $second_pei_type3 副石3配石类型
 * @property string $second_stone_sn3 副石3编号
 * @property string $second_stone_type3 副石3类型
 * @property int $second_stone_num3 副石3粒数
 * @property string $second_stone_weight3 副石3重量(ct)
 * @property string $second_stone_price3 副石3单价/ct
 * @property string $second_stone_amount3 副石3成本(副石3额)
 * @property string $stone_remark 石料备注
 * @property int $parts_way 配件方式
 * @property string $parts_type 配件类型
 * @property int $parts_num 配件数量
 * @property string $parts_material 配件材质
 * @property string $parts_gold_weight 配件金重(g)
 * @property string $parts_price 配件金价
 * @property string $parts_amount 配件成本(配件额)
 * @property string $gong_fee 工费/g
 * @property string $piece_fee 件/工费
 * @property string $basic_gong_fee 基本工费
 * @property int $peishi_num 配石数量
 * @property string $peishi_weight 配石重量(ct)
 * @property string $peishi_fee 配石费
 * @property string $peishi_gong_fee 配石工费
 * @property string $xianqian_price 镶石单价/ct
 * @property string $xianqian_fee 镶石费
 * @property string $second_stone_fee1 镶石1工费/ct
 * @property string $second_stone_fee2 镶石2工费/ct
 * @property string $second_stone_fee3 镶石3工费/ct
 * @property string $penlasha_fee 喷砂费
 * @property string $lasha_fee 拉砂费
 * @property string $bukou_fee 补口费
 * @property string $fense_fee 分色/分件费
 * @property string $biaomiangongyi_fee 表面工艺费
 * @property string $extra_stone_fee 超石费
 * @property string $parts_fee 配件工费/ct
 * @property string $templet_fee 版费
 * @property string $tax_fee 税费
 * @property string $cert_fee 证书费
 * @property string $other_fee 其他费用
 * @property string $total_gong_fee 总工费
 * @property string $factory_cost 工厂成本
 * @property string $tax_amount 税额
 * @property string $cost_price 公司成本价
 * @property string $markup_rate 倍率(加价率)
 * @property string $market_price 标签价(市场价)
 * @property string $factory_mo 模号
 * @property int $is_inlay 是否镶嵌
 * @property int $source_detail_id 来源明细ID
 * @property int $auto_goods_id 手动填写货号
 * @property int $is_auto_price 自动计算价格
 * @property int $auto_loss_weight 自动计算含耗重
 * @property int $auto_gold_amount 自动计算金料额
 * @property int $auto_main_stone 自动计算主石成本
 * @property int $auto_second_stone1 自动计算副石1成本
 * @property int $auto_second_stone2 自动计算副石2成本
 * @property int $auto_second_stone3 自动计算副石3成本
 * @property int $auto_parts_amount 自动计算配件额
 * @property int $auto_peishi_fee 自动计算配石费
 * @property int $auto_xianqian_fee 自动计算镶嵌费
 * @property int $auto_tax_amount 自动计算税额
 * @property int $auto_factory_cost 自动计算工厂成本
 * @property int $is_wholesale 是否批发
 * @property string $produce_sn 布产单号
 * @property string $remark 备注
 * @property int $status 状态
 * @property int $creator_id 创建人
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class WarehouseBillGoodsL extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_bill_goods_l');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bill_id', 'bill_no', 'bill_type'], 'required'],
            [['bill_id', 'goods_num', 'style_id', 'style_cate_id', 'product_type_id', 'style_sex', 'style_channel_id', 'qiban_id', 'qiban_type', 'order_detail_id', 'supplier_id', 'to_warehouse_id', 'put_in_type', 'jintuo_type', 'peiliao_way', 'main_pei_type', 'main_stone_num', 'second_pei_type', 'second_stone_num1', 'second_pei_type2', 'second_stone_num2', 'second_pei_type3', 'second_stone_num3', 'parts_way', 'parts_num', 'peishi_num', 'is_inlay', 'source_detail_id', 'is_auto_price', 'auto_goods_id', 'auto_loss_weight', 'auto_gold_amount', 'auto_main_stone', 'auto_second_stone1', 'auto_second_stone2', 'auto_second_stone3', 'auto_parts_amount', 'auto_peishi_fee', 'auto_xianqian_fee', 'auto_tax_amount', 'auto_factory_cost', 'is_wholesale', 'status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['chain_long', 'gold_weight', 'suttle_weight', 'gold_loss', 'lncl_loss_weight', 'pure_gold', 'pure_gold_rate', 'gold_price', 'gold_amount', 'diamond_carat', 'main_stone_weight', 'main_stone_price', 'main_stone_amount', 'second_stone_weight1', 'second_stone_price1', 'second_stone_amount1', 'second_stone_weight2', 'second_stone_price2', 'second_stone_amount2', 'second_stone_weight3', 'second_stone_price3', 'second_stone_amount3', 'parts_gold_weight', 'parts_price', 'parts_amount', 'gong_fee', 'piece_fee', 'basic_gong_fee', 'peishi_weight', 'peishi_fee', 'peishi_gong_fee', 'xianqian_price', 'xianqian_fee', 'second_stone_fee1', 'second_stone_fee2', 'second_stone_fee3', 'penlasha_fee', 'lasha_fee', 'bukou_fee', 'fense_fee', 'biaomiangongyi_fee', 'extra_stone_fee', 'parts_fee', 'templet_fee', 'tax_fee', 'cert_fee', 'other_fee', 'total_gong_fee', 'tax_amount', 'factory_cost', 'cost_price', 'markup_rate', 'market_price'], 'number'],
            [['bill_no', 'goods_id', 'goods_sn', 'style_sn', 'qiban_sn', 'order_sn', 'length', 'product_size', 'kezi', 'cert_id', 'cert_type', 'diamond_cert_id', 'main_stone_sn', 'main_cert_id', 'second_stone_sn1', 'second_cert_id1', 'second_stone_sn2', 'second_cert_id2', 'second_stone_sn3', 'factory_mo', 'produce_sn'], 'string', 'max' => 30],
            [['bill_type'], 'string', 'max' => 3],
            [['goods_name'], 'string', 'max' => 150],
            [['goods_image', 'stone_remark', 'remark'], 'string', 'max' => 255],
            [['material', 'material_type', 'material_color', 'xiangkou', 'finger', 'finger_hk', 'chain_type', 'cramp_ring', 'talon_head_type', 'xiangqian_craft', 'gross_weight', 'diamond_color', 'diamond_shape', 'diamond_clarity', 'diamond_cut', 'diamond_polish', 'diamond_symmetry', 'diamond_fluorescence', 'diamond_discount', 'diamond_cert_type', 'main_stone_type', 'main_stone_shape', 'main_stone_color', 'main_stone_clarity', 'main_stone_cut', 'main_stone_colour', 'main_cert_type', 'second_stone_type1', 'second_stone_shape1', 'second_stone_color1', 'second_stone_clarity1', 'second_stone_cut1', 'second_stone_colour1', 'second_stone_type2', 'second_stone_shape2', 'second_stone_color2', 'second_stone_clarity2', 'second_stone_colour2', 'second_stone_type3', 'parts_type', 'parts_material'], 'string', 'max' => 10],
            [['biaomiangongyi', 'goods_color', 'main_stone_size', 'second_stone_size1', 'second_stone_size2'], 'string', 'max' => 100],
            [['biaomiangongyi'], 'parseFaceCraft'],
            [['goods_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bill_id' => '单据ID',
            'bill_no' => '单据编号',
            'bill_type' => '单据类型',
            'goods_id' => '货号[条码号]',
            'goods_name' => '商品名称',
            'goods_sn' => '款号/起版号',
            'goods_image' => '商品图片',
            'style_id' => '款式ID',
            'style_sn' => '款号',
            'product_type_id' => '产品线',
            'style_cate_id' => '款式分类',
            'style_sex' => '款式性别',
            'style_channel_id' => '款式渠道',
            'qiban_id' => '起版ID',
            'qiban_sn' => '起版号',
            'qiban_type' => '起版类型',
            'goods_num' => '商品数量',
            'order_sn' => '订单号',
            'order_detail_id' => '订单明细ID',
            'produce_sn' => '布产单号',
            'supplier_id' => '供应商ID',
            'to_warehouse_id' => '入库仓库',
            'put_in_type' => '入库方式',

            //属性信息
            'material' => '主成色',
            'material_type' => '材质',
            'material_color' => '材质颜色',
            'xiangkou' => '戒托镶口(ct)',
            'finger' => '手寸(美号)',
            'finger_hk' => '手寸(港号)',
            'length' => '尺寸(cm)',
            'product_size' => '成品尺寸(mm)',
            'chain_long' => '链长(cm)',
            'chain_type' => '链类型',
            'cramp_ring' => '扣环',
            'talon_head_type' => '爪头形状',
            'xiangqian_craft' => '镶嵌工艺',
            'biaomiangongyi' => '表面工艺',
            'kezi' => '刻字',
            'goods_color' => '货品外部颜色',
            'cert_id' => '成品证书号',
            'cert_type' => '证书类别[成品]',
            'jintuo_type' => '金托类型',

            //金料信息
            'peiliao_way' => '配料方式',
            'gold_weight' => '金重(g)',
            'suttle_weight' => '连石重[净重](g)',
            'lncl_loss_weight' => '含耗重(g)',
            'gold_loss' => '损耗[金损](%)',
            'pure_gold' => '折足(g)',
            'pure_gold_rate' => '折足率(%)',
            'gross_weight' => '毛重(g)',
            'gold_price' => '金价/g',
            'gold_amount' => '金料额',

            //裸钻信息
            'diamond_carat' => '钻石大小(ct)',
            'diamond_color' => '钻石颜色',
            'diamond_shape' => '钻石形状',
            'diamond_clarity' => '钻石净度',
            'diamond_cut' => '钻石切工',
            'diamond_polish' => '钻石抛光',
            'diamond_symmetry' => '钻石对称',
            'diamond_fluorescence' => '钻石荧光',
            'diamond_discount' => '钻石折扣(%)',
            'diamond_cert_type' => '钻石证书类型',
            'diamond_cert_id' => '钻石证书号',

            //主石信息
            'main_pei_type' => '主石配石方式',
            'main_stone_sn' => '主石编号',
            'main_stone_type' => '主石类型',
            'main_stone_num' => '主石粒数',
            'main_stone_weight' => '主石重(ct)',
            'main_stone_shape' => '主石形状',
            'main_stone_color' => '主石颜色',
            'main_stone_clarity' => '主石净度',
            'main_stone_cut' => '主石切工',
            'main_stone_colour' => '主石色彩',
            'main_stone_size' => '主石规格',
            'main_cert_id' => '主石证书号',
            'main_cert_type' => '主石证书类型',
            'main_stone_price' => '主石单价/ct',
            'main_stone_amount' => '主石成本价',

            //副石1信息
            'second_pei_type' => '副石1配石方式',
            'second_stone_sn1' => '副石1编号',
            'second_stone_type1' => '副石1类型',
            'second_stone_num1' => '副石1粒数',
            'second_stone_weight1' => '副石1重(ct)',
            'second_stone_shape1' => '副石1形状',
            'second_stone_color1' => '副石1颜色',
            'second_stone_clarity1' => '副石1净度',
            'second_stone_cut1' => '副石1切工',
            'second_stone_colour1' => '副石1色彩',
            'second_stone_size1' => '副石1规格',
            'second_cert_id1' => '副石1证书号',
            'second_stone_price1' => '副石1单价/ct',
            'second_stone_amount1' => '副石1成本价',
            'second_stone_fee1' => '镶石1工费',

            //副石2信息
            'second_pei_type2' => '副石2配石方式',
            'second_stone_sn2' => '副石2编号',
            'second_stone_type2' => '副石2类型',
            'second_stone_num2' => '副石2粒数',
            'second_stone_weight2' => '副石2重(ct)',
            'second_stone_shape2' => '副石2形状',
            'second_stone_color2' => '副石2颜色',
            'second_stone_clarity2' => '副石2净度',
            'second_stone_colour2' => '副石2色彩',
            'second_stone_size2' => '副石2规格',
            'second_cert_id2' => '副石2证书号',
            'second_stone_price2' => '副石2单价/ct',
            'second_stone_amount2' => '副石2成本价',
            'second_stone_fee2' => '镶石2工费',

            //副石3信息
            'second_pei_type3' => '副石3配石方式',
            'second_stone_sn3' => '副石3编号',
            'second_stone_type3' => '副石3类型',
            'second_stone_num3' => '副石3数量',
            'second_stone_weight3' => '副石3重量(ct)',
            'second_stone_price3' => '副石3单价/ct',
            'second_stone_amount3' => '副石3成本价',
            'second_stone_fee3' => '镶石3工费',
            'stone_remark' => '石料备注',

            //配件信息
            'parts_way' => '配件方式',
            'parts_type' => '配件类型',
            'parts_num' => '配件数量',
            'parts_material' => '配件材质',
            'parts_gold_weight' => '配件金重(g)',
            'parts_price' => '配件金价/g',
            'parts_amount' => '配件总额',

            //工费信息
            'gong_fee' => '克工费/g',
            'piece_fee' => '件/工费',
            'basic_gong_fee' => '基本工费',
            'peishi_num' => '配石数量',
            'peishi_weight' => '配石重量(ct)',
            'peishi_gong_fee' => '配石工费/ct',
            'peishi_fee' => '配石费',
            'xianqian_price' => '镶石单价/颗',
            'xianqian_fee' => '镶石费',
            'parts_fee' => '配件工费',
            'templet_fee' => '版费',
            'penlasha_fee' => '喷沙费',
            'lasha_fee' => '拉沙费',
            'bukou_fee' => '补口费',
            'extra_stone_fee' => '超石费',
            'fense_fee' => '分色/分件费',
            'biaomiangongyi_fee' => '表面工艺费',
            'tax_fee' => '税费',
            'cert_fee' => '证书费',
            'other_fee' => '其它工费',
            'total_gong_fee' => '总工费',

            //价格信息
            'tax_amount' => '税额',
            'factory_cost' => '工厂总成本',
            'markup_rate' => '倍率[加价率]',
            'market_price' => '标签价(市场价)',
            'cost_price' => '公司总成本(成本价)',

            //其他信息
            'factory_mo' => '工厂模号',
            'is_inlay' => '是否镶嵌',
            'is_wholesale' => '是否批发',
            'auto_goods_id' => '是否手动录入货号',
            'is_auto_price' => '是否自动计算价格',
            'auto_loss_weight' => '自动计算含耗重',
            'auto_gold_amount' => '自动计算金料额',
            'auto_main_stone' => '自动计算主石成本',
            'auto_second_stone1' => '自动计算副石1成本',
            'auto_second_stone2' => '自动计算副石2成本',
            'auto_second_stone3' => '自动计算副石3成本',
            'auto_parts_amount' => '自动计算配件额',
            'auto_peishi_fee' => '自动计算配石费',
            'auto_xianqian_fee' => '自动计算镶嵌费',
            'auto_factory_cost' => '自动计算工厂成本',
            'auto_tax_amount' => '自动计算税额',
            'source_detail_id' => '来源明细ID',
            'remark' => '备注',
            'status' => '状态',
            'creator_id' => '创建人',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * 表面工艺
     */
    public function parseFaceCraft()
    {
        if (is_array($this->biaomiangongyi)) {
            $this->biaomiangongyi = ',' . implode(',', $this->biaomiangongyi) . ',';
        }
        return $this->biaomiangongyi;
    }

    /**
     * 关联产品线分类一对一
     * @return \yii\db\ActiveQuery
     */
    public function getProductType()
    {
        return $this->hasOne(ProductType::class, ['id' => 'product_type_id']);
    }

    /**
     * 关联款式分类一对一
     * @return \yii\db\ActiveQuery
     */
    public function getStyleCate()
    {
        return $this->hasOne(StyleCate::class, ['id' => 'style_cate_id']);
    }

    /**
     * 入库仓库 一对一
     * @return \yii\db\ActiveQuery
     */
    public function getToWarehouse()
    {
        return $this->hasOne(Warehouse::class, ['id' => 'to_warehouse_id'])->alias('toWarehouse');
    }
}
