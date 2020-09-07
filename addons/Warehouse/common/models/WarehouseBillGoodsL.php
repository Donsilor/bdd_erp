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
 * @property string $goods_sn 款号/起版号
 * @property string $goods_image 商品图片
 * @property int $style_id 款式ID/起版ID
 * @property string $style_sn 款号
 * @property int $product_type_id 产品线
 * @property int $style_cate_id 款式分类
 * @property int $style_sex 款式性别
 * @property int $style_channel_id 款式渠道
 * @property string $qiban_sn 起版号
 * @property int $qiban_type 起版类型
 * @property string $order_sn 订单号
 * @property int $order_detail_id 订单明细ID
 * @property int $supplier_id 供应商ID
 * @property int $put_in_type 入库方式
 * @property string $produce_sn 布产单号
 * @property int $is_wholesale 是否批发
 * @property int $peiliao_way 配料方式
 * @property string $gold_weight 金重
 * @property string $gold_loss 金损
 * @property string $suttle_weight 净重
 * @property string $lncl_loss_weight 含耗重(g)
 * @property string $gold_price 金价
 * @property string $gold_amount 金料额
 * @property string $gross_weight 毛重
 * @property string $finger 手寸(美)
 * @property string $finger_hk 手寸(港)
 * @property string $product_size 尺寸
 * @property string $kezi 刻字
 * @property string $cert_type 证书类别
 * @property string $cert_id 证书号
 * @property int $goods_num 商品数量
 * @property string $material 主成色
 * @property string $material_type 材质
 * @property string $material_color 材质颜色
 * @property string $diamond_carat 钻石大小
 * @property string $diamond_color 钻石颜色
 * @property string $diamond_shape 钻石形状
 * @property string $diamond_clarity 钻石净度
 * @property string $diamond_cut 钻石切工
 * @property string $diamond_polish 钻石抛光
 * @property string $diamond_symmetry 钻石对称
 * @property string $diamond_fluorescence 钻石荧光
 * @property string $diamond_discount 钻石折扣
 * @property string $diamond_cert_type 钻石证书类型
 * @property string $diamond_cert_id 钻石证书号
 * @property int $jintuo_type 金托类型
 * @property string $market_price 市场价(标签价)
 * @property string $cost_price 成本价
 * @property string $gong_fee 工费
 * @property string $basic_gong_fee 基本工费
 * @property string $bukou_fee 补口费
 * @property string $xianqian_price 镶石单价/ct
 * @property string $xianqian_fee 镶石费
 * @property string $cert_fee 证书费
 * @property string $markup_rate 倍率
 * @property string $extra_stone_fee 超石费
 * @property string $tax_fee 税费
 * @property string $fense_fee 分色/分件费
 * @property string $other_fee 其他费用
 * @property string $biaomiangongyi_fee 表面工艺费
 * @property string $penlasha_fee 喷拉砂费
 * @property string $templet_fee 版费
 * @property string $total_gong_fee 总工费
 * @property string $xiangkou 戒托镶口
 * @property string $length 长度
 * @property string $biaomiangongyi 表面工艺
 * @property string $factory_mo 模号
 * @property string $factory_cost 工厂成本
 * @property int $is_inlay 是否镶嵌
 * @property double $chain_long 链长(mm)
 * @property string $chain_type 链类型
 * @property string $cramp_ring 扣环
 * @property string $talon_head_type 爪头形状
 * @property string $xiangqian_craft 镶嵌工艺
 * @property int $parts_type 配件类型
 * @property string $parts_material 配件材质
 * @property string $parts_amount 配件总额
 * @property int $parts_way 配件方式
 * @property string $parts_gold_weight 配件金重(g)
 * @property string $parts_price 配件金价
 * @property string $parts_fee 配件工费/ct
 * @property int $parts_num 配件数量
 * @property string $goods_color 货品外部颜色
 * @property int $main_pei_type 主石配石类型
 * @property string $main_stone_sn 主石编号
 * @property string $main_cert_id 主石证书号
 * @property string $main_cert_type 主石证书类型
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
 * @property string $main_stone_amount 主石总额(成本价)
 * @property int $second_pei_type 副石配石类型
 * @property string $second_stone_sn1 副石1编号
 * @property string $second_cert_id1 副石1证书号
 * @property string $second_stone_type1 副石1类型
 * @property int $second_stone_num1 副石1粒数
 * @property string $second_stone_weight1 副石1重
 * @property string $second_stone_shape1 副石1形状
 * @property string $second_stone_color1 副石1颜色
 * @property string $second_stone_clarity1 副石1净度
 * @property string $second_stone_cut1 副石1切工
 * @property string $second_stone_colour1 副石1色彩
 * @property string $second_stone_size1 副石1规格
 * @property string $second_stone_price1 副石1总计价
 * @property string $second_stone_amount1 副石1总额(成本价)
 * @property int $second_pei_type2 副石2配石类型
 * @property string $second_stone_sn2 副石2编号(石包号)
 * @property string $second_cert_id2 副石2证书号
 * @property string $second_stone_type2 副石2类型
 * @property int $second_stone_num2 副石2粒数
 * @property string $second_stone_weight2 副石2重
 * @property string $second_stone_shape2 副石2形状
 * @property string $second_stone_color2 副石2颜色
 * @property string $second_stone_clarity2 副石2净度
 * @property string $second_stone_colour2 副石2色彩
 * @property string $second_stone_size2 副石2规格
 * @property string $second_stone_price2 副石2总计价
 * @property string $second_stone_amount2 副石2总额(成本价)
 * @property string $second_pei_type3 副石3配石类型
 * @property string $second_stone_type3 副石3类型
 * @property int $second_stone_num3 副石3数量
 * @property string $second_stone_weight3 副石3重量(ct)
 * @property string $second_stone_price3 副石3买入单价
 * @property string $second_stone_amount3 副石3总额(成本价)
 * @property string $stone_remark 石料备注
 * @property int $peishi_num 配石数量
 * @property string $peishi_weight 配石重量
 * @property string $peishi_fee 配石费
 * @property string $peishi_gong_fee 配石工费
 * @property int $source_detail_id 来源明细ID
 * @property int $auto_goods_id 是否手动录入货号
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
            [['bill_id', 'style_id', 'product_type_id', 'style_cate_id', 'style_sex', 'style_channel_id', 'qiban_type', 'order_detail_id', 'supplier_id', 'put_in_type', 'is_wholesale', 'goods_num', 'jintuo_type', 'is_inlay', 'peiliao_way', 'parts_type', 'parts_way', 'parts_num', 'main_pei_type', 'main_stone_num', 'second_pei_type', 'second_stone_num1', 'second_pei_type2', 'second_stone_num2', 'second_stone_num3', 'second_pei_type3', 'peishi_num', 'source_detail_id', 'auto_goods_id', 'status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['gold_weight', 'gold_loss', 'suttle_weight', 'lncl_loss_weight', 'gold_price', 'gold_amount', 'diamond_carat', 'market_price', 'cost_price', 'gong_fee', 'basic_gong_fee', 'bukou_fee', 'xianqian_price', 'xianqian_fee', 'cert_fee', 'markup_rate', 'extra_stone_fee', 'tax_fee', 'fense_fee', 'other_fee', 'biaomiangongyi_fee', 'penlasha_fee', 'templet_fee', 'total_gong_fee', 'factory_cost', 'chain_long', 'parts_amount', 'parts_gold_weight', 'parts_price', 'parts_fee', 'main_stone_weight', 'main_stone_price', 'main_stone_amount', 'second_stone_weight1', 'second_stone_price1', 'second_stone_amount1', 'second_stone_weight2', 'second_stone_price2', 'second_stone_amount2', 'second_stone_weight3', 'second_stone_price3', 'second_stone_amount3', 'peishi_weight', 'peishi_fee', 'peishi_gong_fee'], 'number'],
            [['bill_no', 'goods_id', 'goods_sn', 'style_sn', 'qiban_sn', 'produce_sn', 'main_stone_sn', 'main_cert_id', 'second_stone_sn1', 'second_stone_sn2', 'second_cert_id2'], 'string', 'max' => 30],
            [['bill_type'], 'string', 'max' => 3],
            [['goods_name', 'goods_image', 'product_size', 'cert_id', 'length', 'goods_color', 'main_stone_size', 'second_stone_size1', 'second_stone_size2'], 'string', 'max' => 100],
            [['order_sn'], 'string', 'max' => 40],
            [['gross_weight', 'diamond_cert_id', 'second_cert_id1'], 'string', 'max' => 20],
            [['finger', 'finger_hk', 'material', 'material_type', 'material_color', 'diamond_color', 'diamond_shape', 'diamond_clarity', 'diamond_cut', 'diamond_polish', 'diamond_symmetry', 'diamond_fluorescence', 'diamond_discount', 'diamond_cert_type', 'xiangkou', 'biaomiangongyi', 'chain_type', 'cramp_ring', 'talon_head_type', 'xiangqian_craft', 'parts_material', 'main_stone_type', 'main_cert_type', 'main_stone_shape', 'main_stone_color', 'main_stone_clarity', 'main_stone_cut', 'main_stone_colour', 'second_stone_type1', 'second_stone_shape1', 'second_stone_color1', 'second_stone_clarity1', 'second_stone_cut1', 'second_stone_colour1', 'second_stone_type2', 'second_stone_shape2', 'second_stone_color2', 'second_stone_clarity2', 'second_stone_colour2', 'second_stone_type3'], 'string', 'max' => 10],
            [['kezi', 'cert_type', 'factory_mo'], 'string', 'max' => 50],
            [['stone_remark', 'remark'], 'string', 'max' => 255],
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
            'style_id' => '款式ID/起版ID',
            'style_sn' => '款号',
            'product_type_id' => '产品线',
            'style_cate_id' => '款式分类',
            'style_sex' => '款式性别',
            'style_channel_id' => '款式渠道',
            'qiban_sn' => '起版号',
            'qiban_type' => '起版类型',
            'order_sn' => '订单号',
            'order_detail_id' => '订单明细ID',
            'supplier_id' => '供应商ID',
            'put_in_type' => '入库方式',
            'produce_sn' => '布产单号',
            'is_wholesale' => '是否批发',
            'peiliao_way' => '配料方式',
            'gold_weight' => '金重(g)',
            'gold_loss' => '损耗[金损](%)',
            'suttle_weight' => '连石重[净重](g)',
            'lncl_loss_weight' => '含耗重(g)',
            'gold_price' => '金价/g',
            'gold_amount' => '金料额',
            'gross_weight' => '毛重(g)',
            'finger' => '手寸(美号)',
            'finger_hk' => '手寸(港号)',
            'product_size' => '成品尺寸(mm)',
            'kezi' => '刻字',
            'cert_type' => '证书类别[成品]',
            'cert_id' => '成品证书号',
            'goods_num' => '商品数量',
            'material' => '主成色',
            'material_type' => '材质',
            'material_color' => '材质颜色',
            'diamond_carat' => '钻石大小(ct)',
            'diamond_color' => '钻石颜色',
            'diamond_shape' => '钻石形状',
            'diamond_clarity' => '钻石净度',
            'diamond_cut' => '钻石切工',
            'diamond_polish' => '钻石抛光',
            'diamond_symmetry' => '钻石对称',
            'diamond_fluorescence' => '钻石荧光',
            'diamond_discount' => '钻石折扣',
            'diamond_cert_type' => '钻石证书类型',
            'diamond_cert_id' => '钻石证书号',
            'jintuo_type' => '金托类型',
            'market_price' => '标签价(市场价)',
            'cost_price' => '公司总成本(成本价)',
            'gong_fee' => '克工费/g',
            'basic_gong_fee' => '基本工费',
            'bukou_fee' => '补口费',
            'xianqian_price' => '镶石单价/颗',
            'xianqian_fee' => '镶石费',
            'cert_fee' => '证书费',
            'markup_rate' => '倍率[加价率]',
            'extra_stone_fee' => '超石费',
            'tax_fee' => '税费',
            'fense_fee' => '分色/分件费',
            'other_fee' => '其他费用',
            'biaomiangongyi_fee' => '表面工艺费',
            'penlasha_fee' => '喷拉沙费',
            'templet_fee' => '版费',
            'total_gong_fee' => '总工费',
            'xiangkou' => '戒托镶口(ct)',
            'length' => '尺寸(cm)',
            'biaomiangongyi' => '表面工艺',
            'factory_mo' => '工厂模号',
            'factory_cost' => '工厂成本',
            'is_inlay' => '是否镶嵌',
            'chain_long' => '链长(cm)',
            'chain_type' => '链类型',
            'cramp_ring' => '扣环',
            'talon_head_type' => '爪头形状',
            'xiangqian_craft' => '镶嵌工艺',
            'parts_type' => '配件类型',
            'parts_material' => '配件材质',
            'parts_amount' => '配件总额',
            'parts_way' => '配件方式',
            'parts_gold_weight' => '配件金重(g)',
            'parts_price' => '配件金价/g',
            'parts_fee' => '配件工费',
            'parts_num' => '配件数量',
            'goods_color' => '货品外部颜色',
            'main_pei_type' => '主石配石方式',
            'main_stone_sn' => '主石编号',
            'main_cert_id' => '主石证书号',
            'main_cert_type' => '主石证书类型',
            'main_stone_type' => '主石类型',
            'main_stone_num' => '主石粒数',
            'main_stone_weight' => '主石重(ct)',
            'main_stone_shape' => '主石形状',
            'main_stone_color' => '主石颜色',
            'main_stone_clarity' => '主石净度',
            'main_stone_cut' => '主石切工',
            'main_stone_colour' => '主石色彩',
            'main_stone_size' => '主石规格',
            'main_stone_price' => '主石单价/ct',
            'main_stone_amount' => '主石成本价',
            'second_pei_type' => '副石1配石方式',
            'second_stone_sn1' => '副石1编号',
            'second_cert_id1' => '副石1证书号',
            'second_stone_type1' => '副石1类型',
            'second_stone_num1' => '副石1粒数',
            'second_stone_weight1' => '副石1重(ct)',
            'second_stone_shape1' => '副石1形状',
            'second_stone_color1' => '副石1颜色',
            'second_stone_clarity1' => '副石1净度',
            'second_stone_cut1' => '副石1切工',
            'second_stone_colour1' => '副石1色彩',
            'second_stone_size1' => '副石1规格',
            'second_stone_price1' => '副石1单价/ct',
            'second_stone_amount1' => '副石1成本价',
            'second_pei_type2' => '副石2配石方式',
            'second_stone_sn2' => '副石2编号',
            'second_cert_id2' => '副石2证书号',
            'second_stone_type2' => '副石2类型',
            'second_stone_num2' => '副石2粒数',
            'second_stone_weight2' => '副石2重(ct)',
            'second_stone_shape2' => '副石2形状',
            'second_stone_color2' => '副石2颜色',
            'second_stone_clarity2' => '副石2净度',
            'second_stone_colour2' => '副石2色彩',
            'second_stone_size2' => '副石2规格',
            'second_stone_price2' => '副石2单价/ct',
            'second_stone_amount2' => '副石2成本价',
            'second_pei_type3' => '副石3配石方式',
            'second_stone_type3' => '副石3类型',
            'second_stone_num3' => '副石3数量',
            'second_stone_weight3' => '副石3重量(ct)',
            'second_stone_price3' => '副石3单价/ct',
            'second_stone_amount3' => '副石3成本价',
            'stone_remark' => '石料备注',
            'peishi_num' => '配石数量',
            'peishi_weight' => '配石重量(ct)',
            'peishi_fee' => '配石费',
            'peishi_gong_fee' => '配石工费/ct',
            'source_detail_id' => '来源明细ID',
            'auto_goods_id' => '是否手动录入货号',
            'remark' => '备注',
            'status' => '状态',
            'creator_id' => '创建人',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
    /**
     * 关联产品线分类一对一
     * @return \yii\db\ActiveQuery
     */
    public function getProductType()
    {
        return $this->hasOne(ProductType::class, ['id'=>'product_type_id']);
    }
    /**
     * 关联款式分类一对一
     * @return \yii\db\ActiveQuery
     */
    public function getStyleCate()
    {
        return $this->hasOne(StyleCate::class, ['id'=>'style_cate_id']);
    }
}
