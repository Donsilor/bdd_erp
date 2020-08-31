<?php

namespace addons\Purchase\common\models;

use Yii;
use addons\Style\common\models\StyleCate;
use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleChannel;

/**
 * This is the model class for table "purchase_receipt_goods".
 *
 * @property int $id ID
 * @property int $receipt_id 采购收货单ID
 * @property string $purchase_sn 采购单编号
 * @property string $order_sn 订单号
 * @property string $produce_sn 布产单编号
 * @property int $xuhao 序号
 * @property int $goods_status 货品状态
 * @property string $barcode 条形码
 * @property string $goods_name 货品名称
 * @property int $goods_num 货品数量
 * @property string $goods_sn 款号/起版号
 * @property string $style_sn 款式编号
 * @property string $style_cate_id 款式分类
 * @property string $product_type_id 产品线
 * @property int $style_sex 款式性别
 * @property int $style_channel_id 款式渠道
 * @property string $qiban_sn 起版号
 * @property int $qiban_type 起版类型
 * @property string $finger 手寸(美号)
 * @property string $finger_hk 手寸(港号)
 * @property string $xiangkou 镶口(ct)
 * @property string $material 主成色
 * @property string $material_type 材质
 * @property string $material_color 材质颜色
 * @property int $peiliao_way 配料方式
 * @property string $gold_weight 连石重[金重](g)
 * @property string $gold_price 金价单/g
 * @property double $gold_amount 金料额
 * @property int $jintuo_type 金托类型
 * @property double $gold_loss 耗损[金损](%)
 * @property double $gross_weight 毛重(g)
 * @property string $lncl_loss_weight 含耗重(g)
 * @property double $suttle_weight 净重(g)
 * @property int $is_inlay 是否镶嵌
 * @property string $kezi 刻字内容
 * @property string $cost_price 成本价
 * @property string $market_price 市场价
 * @property string $sale_price 销售价
 * @property string $goods_color 货品外部颜色
 * @property int $parts_num 配件数量
 * @property double $parts_weight 配件重量(g)
 * @property string $parts_price 配件金额
 * @property string $product_size 成品尺寸(cm)
 * @property string $length 尺寸[长度](cm)
 * @property int $biaomiangongyi 表面工艺
 * @property double $single_stone_weight 单件连石重(g)
 * @property double $chain_long 链长(cm)
 * @property string $chain_type 链类型
 * @property string $cramp_ring 扣环
 * @property string $talon_head_type 爪头形状
 * @property string $xiangqian_craft 镶嵌工艺
 * @property int $parts_type 配件类型
 * @property string $parts_material 配件材质
 * @property string $parts_gold_weight 配件金重(g)
 * @property string $parts_amount 配件总额
 * @property int $parts_way 配件方式
 * @property string $goods_remark 商品备注
 * @property int $put_in_type 入库方式
 * @property int $to_warehouse_id 入库仓库
 * @property int $iqc_reason 质检未过原因
 * @property string $iqc_remark 质检备注
 * @property string $cert_id 证书号
 * @property string $cert_type 证书类型
 * @property int $main_pei_type 主石配石类型
 * @property string $main_cert_id 主石证书号
 * @property string $main_cert_type 主石证书类型
 * @property string $main_stone 主石类型
 * @property string $main_stone_sn 主石编号
 * @property int $main_stone_num 主石数量
 * @property double $main_stone_weight 主石重(ct)
 * @property string $main_stone_shape 主石形状
 * @property string $main_stone_color 主石颜色
 * @property string $main_stone_clarity 主石净度
 * @property string $main_stone_cut 主石切工
 * @property string $main_stone_symmetry 主石对称
 * @property string $main_stone_polish 主石抛光
 * @property string $main_stone_fluorescence 主石荧光
 * @property string $main_stone_colour 主石色彩
 * @property string $main_stone_size 主石规格
 * @property string $main_stone_price 主石买入单价
 * @property string $main_stone_amount 主石总额(成本价)
 * @property int $second_pei_type 副石配石类型
 * @property string $second_cert_id1 副石证书号
 * @property string $second_stone1 副石1类型
 * @property int $second_stone_num1 副石1数量
 * @property string $second_stone_sn1 副石1编号
 * @property double $second_stone_weight1 副石1重量(ct)
 * @property string $second_stone_shape1 副石形状
 * @property string $second_stone_color1 副石颜色
 * @property string $second_stone_clarity1 副石净度
 * @property string $second_stone_colour1 副石色彩
 * @property string $second_stone_size1 副石1规格
 * @property string $second_stone_price1 副石1买入单价
 * @property string $second_stone_amount1 副石1总额(成本价)
 * @property int $second_pei_type2 副石2配石类型
 * @property string $second_stone_sn2 副石2编号(石包号)
 * @property string $second_cert_id2 副石2证书号
 * @property string $second_stone2 副石2类型
 * @property int $second_stone_num2 副石2数量
 * @property double $second_stone_weight2 副石2重量(ct)
 * @property string $second_stone_shape2 副石2形状
 * @property string $second_stone_color2 副石2颜色
 * @property string $second_stone_clarity2 副石2净度
 * @property string $second_stone_colour2 副石2色彩
 * @property string $second_stone_price2 副石2买入单价
 * @property string $second_stone_amount2 副石2总额(成本价)
 * @property int $second_pei_type3 副石3配石类型
 * @property string $second_stone_size2 副石2规格
 * @property string $second_stone3 副石3类型
 * @property int $second_stone_num3 副石3数量
 * @property double $second_stone_weight3 副石3重量(ct)
 * @property string $second_stone_price3 副石3买入单价/ct
 * @property string $second_stone_amount3 副石3总额(成本价)
 * @property string $peishi_fee 配石费
 * @property string $peishi_gong_fee 配石工费
 * @property string $stone_remark 石料备注
 * @property string $factory_mo 工厂模号
 * @property string $factory_cost 工厂成本
 * @property double $markup_rate 倍率(加价率)
 * @property string $gong_fee 克工费/g
 * @property string $basic_gong_fee 基本工费
 * @property string $parts_fee 配件工费
 * @property string $xianqian_fee 镶嵌工费
 * @property string $biaomiangongyi_fee 表面工艺工费
 * @property string $penlasha_fee 喷拉砂费
 * @property string $templet_fee 版费
 * @property string $fense_fee 分色\分件费
 * @property string $bukou_fee 补口工费
 * @property string $xianqian_price 镶石单价/ct
 * @property string $cert_fee 证书费
 * @property string $extra_stone_fee 超石费
 * @property string $tax_fee 税费
 * @property string $other_fee 其他费用
 * @property string $total_gong_fee 总工费
 * @property int $sort 排序
 * @property int $status 状态 1启用 0禁用 -1 删除
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class PurchaseReceiptGoods extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('purchase_receipt_goods');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['receipt_id', 'purchase_sn'], 'required'],
            [['id', 'receipt_id', 'xuhao', 'goods_status', 'goods_num', 'style_sex', 'style_channel_id', 'qiban_type', 'peiliao_way', 'jintuo_type', 'is_inlay', 'biaomiangongyi', 'parts_type', 'parts_way', 'parts_num', 'put_in_type', 'to_warehouse_id', 'iqc_reason', 'main_pei_type', 'main_stone_num', 'second_pei_type', 'second_stone_num1', 'second_pei_type2', 'second_stone_num2', 'second_pei_type3', 'second_stone_num3', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['gold_weight', 'gold_price', 'gold_amount', 'gold_loss', 'gross_weight', 'lncl_loss_weight', 'suttle_weight', 'cost_price', 'market_price', 'sale_price', 'parts_weight', 'parts_price', 'parts_gold_weight', 'single_stone_weight', 'chain_long', 'parts_amount', 'main_stone_weight', 'main_stone_price', 'main_stone_amount', 'second_stone_weight1', 'second_stone_price1', 'second_stone_amount1', 'second_stone_weight2', 'second_stone_price2', 'second_stone_amount2', 'second_stone_weight3', 'second_stone_price3', 'second_stone_amount3', 'peishi_fee', 'peishi_gong_fee', 'factory_cost', 'markup_rate', 'gong_fee', 'basic_gong_fee', 'parts_fee', 'xianqian_fee', 'biaomiangongyi_fee', 'penlasha_fee', 'templet_fee', 'fense_fee', 'bukou_fee', 'xianqian_price', 'cert_fee', 'extra_stone_fee', 'tax_fee', 'other_fee', 'total_gong_fee'], 'number'],
            [['purchase_sn', 'order_sn', 'produce_sn', 'qiban_sn', 'cert_id', 'second_cert_id1', 'second_stone_sn2', 'second_cert_id2'], 'string', 'max' => 30],
            [['barcode', 'kezi', 'goods_color', 'product_size', 'cert_type', 'main_cert_type', 'main_stone_size', 'second_stone_size1', 'second_stone_size2'], 'string', 'max' => 100],
            [['goods_name', 'goods_remark', 'iqc_remark', 'stone_remark'], 'string', 'max' => 255],
            [['goods_sn', 'style_sn', 'factory_mo', 'length'], 'string', 'max' => 50],
            [['style_cate_id', 'product_type_id', 'finger', 'finger_hk', 'xiangkou', 'material', 'material_type', 'material_color', 'chain_type', 'cramp_ring', 'talon_head_type', 'xiangqian_craft', 'parts_material', 'main_stone', 'main_stone_shape', 'main_stone_color', 'main_stone_clarity', 'main_stone_cut', 'main_stone_symmetry', 'main_stone_polish', 'main_stone_fluorescence', 'main_stone_colour', 'second_stone1', 'second_stone_shape1', 'second_stone_color1', 'second_stone_clarity1', 'second_stone_colour1', 'second_stone2', 'second_stone_shape2', 'second_stone_color2', 'second_stone_clarity2', 'second_stone_colour2', 'second_stone3'], 'string', 'max' => 10],
            [['main_cert_id'], 'string', 'max' => 200],
            [['main_stone_sn', 'second_stone_sn1'], 'string', 'max' => 20],
            [['supplier_id', 'receipt_no'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'receipt_id' => '采购收货单ID',
            'purchase_sn' => '采购单编号',
            'order_sn' => '订单号',
            'produce_sn' => '布产单编号',
            'xuhao' => '序号',
            'goods_status' => '货品状态',
            'barcode' => '条形码',
            'goods_name' => '货品名称',
            'goods_num' => '货品数量',
            'goods_sn' => '款号/起版号',
            'style_sn' => '款式编号',
            'style_cate_id' => '款式分类',
            'product_type_id' => '产品线',
            'style_sex' => '款式性别',
            'style_channel_id' => '款式渠道',
            'qiban_sn' => '起版号',
            'qiban_type' => '起版类型',
            'finger' => '手寸(美号)',
            'finger_hk' => '手寸(港号)',
            'xiangkou' => '镶口(ct)',
            'material' => '主成色',
            'material_type' => '材质',
            'material_color' => '材质颜色',
            'peiliao_way' => '配料方式',
            'gold_weight' => '连石重[金重](g)',
            'gold_price' => '金价单/g',
            'gold_amount' => '金料额',
            'jintuo_type' => '金托类型',
            'gold_loss' => '耗损[金损](%)',
            'gross_weight' => '毛重(g)',
            'lncl_loss_weight' => '含耗重(g)',
            'suttle_weight' => '净重(g)',
            'is_inlay' => '是否镶嵌',
            'kezi' => '刻字内容',
            'cost_price' => '成本价',
            'market_price' => '市场价',
            'sale_price' => '销售价',
            'goods_color' => '货品外部颜色',
            'parts_num' => '配件数量',
            'parts_weight' => '配件重量(g)',
            'parts_gold_weight' => '配件金重(g)',
            'parts_price' => '配件金额',
            'product_size' => '成品尺寸(cm)',
            'length' => '尺寸[长度](cm)',
            'biaomiangongyi' => '表面工艺',
            'single_stone_weight' => '单件连石重(g)',
            'chain_long' => '链长(cm)',
            'chain_type' => '链类型',
            'cramp_ring' => '扣环',
            'talon_head_type' => '爪头形状',
            'xiangqian_craft' => '镶嵌工艺',
            'parts_type' => '配件类型',
            'parts_material' => '配件材质',
            'parts_amount' => '配件总额',
            'parts_way' => '配件方式',
            'goods_remark' => '商品备注',
            'put_in_type' => '入库方式',
            'to_warehouse_id' => '入库仓库',
            'iqc_reason' => '质检未过原因',
            'iqc_remark' => '质检备注',
            'cert_id' => '成品证书号',
            'cert_type' => '证书类别[成品]',
            'main_pei_type' => '主石配石类型',
            'main_cert_id' => '主石证书号',
            'main_cert_type' => '主石证书类型',
            'main_stone' => '主石类型',
            'main_stone_sn' => '主石编号',
            'main_stone_num' => '主石数量',
            'main_stone_weight' => '主石重(ct)',
            'main_stone_shape' => '主石形状',
            'main_stone_color' => '主石颜色',
            'main_stone_clarity' => '主石净度',
            'main_stone_cut' => '主石切工',
            'main_stone_symmetry' => '主石对称',
            'main_stone_polish' => '主石抛光',
            'main_stone_fluorescence' => '主石荧光',
            'main_stone_colour' => '主石色彩',
            'main_stone_size' => '主石规格',
            'main_stone_price' => '主石买入单价',
            'main_stone_amount' => '主石总额(成本价)',
            'second_pei_type' => '副石配石类型',
            'second_cert_id1' => '副石证书号',
            'second_stone1' => '副石1类型',
            'second_stone_num1' => '副石1数量',
            'second_stone_sn1' => '副石1编号',
            'second_stone_weight1' => '副石1重量(ct)',
            'second_stone_shape1' => '副石形状',
            'second_stone_color1' => '副石颜色',
            'second_stone_clarity1' => '副石净度',
            'second_stone_colour1' => '副石色彩',
            'second_stone_size1' => '副石1规格',
            'second_stone_price1' => '副石1单价/ct',
            'second_stone_amount1' => '副石1总额(成本价)',
            'second_pei_type2' => '副石2配石类型',
            'second_stone_sn2' => '副石2编号(石包号)',
            'second_cert_id2' => '副石2证书号',
            'second_stone2' => '副石2类型',
            'second_stone_num2' => '副石2数量',
            'second_stone_weight2' => '副石2重量(ct)',
            'second_stone_shape2' => '副石2形状',
            'second_stone_color2' => '副石2颜色',
            'second_stone_clarity2' => '副石2净度',
            'second_stone_colour2' => '副石2色彩',
            'second_stone_price2' => '副石2单价/ct',
            'second_stone_amount2' => '副石2总额(成本价)',
            'second_pei_type3' => '副石3配石类型',
            'second_stone_size2' => '副石2规格',
            'second_stone3' => '副石3类型',
            'second_stone_num3' => '副石3数量',
            'second_stone_weight3' => '副石3重量(ct)',
            'second_stone_price3' => '副石3单价/ct',
            'second_stone_amount3' => '副石3总额(成本价)',
            'peishi_fee' => '配石费',
            'peishi_gong_fee' => '配石工费',
            'stone_remark' => '石料备注',
            'factory_mo' => '工厂模号',
            'factory_cost' => '工厂成本',
            'markup_rate' => '倍率(加价率)',
            'gong_fee' => '克工费/g',
            'basic_gong_fee' => '基本工费',
            'parts_fee' => '配件工费',
            'xianqian_fee' => '镶嵌工费',
            'biaomiangongyi_fee' => '表面工艺工费',
            'penlasha_fee' => '喷拉砂费',
            'templet_fee' => '版费',
            'fense_fee' => '分色\分件费',
            'bukou_fee' => '补口工费',
            'xianqian_price' => '镶石单价/ct',
            'cert_fee' => '证书费',
            'extra_stone_fee' => '超石费',
            'tax_fee' => '税费',
            'other_fee' => '其他费用',
            'total_gong_fee' => '总工费',
            'sort' => '排序',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * 关联采购收货单明细表
     * @return \yii\db\ActiveQuery
     */
    public function getReceipt(){
        return $this->hasOne(PurchaseReceipt::class, ['id'=>'receipt_id'])->alias('receipt');
    }
    /**
     * 关联产品线分类一对一
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(ProductType::class, ['id'=>'product_type_id'])->alias('type');
    }
    /**
     * 关联款式分类一对一
     * @return \yii\db\ActiveQuery
     */
    public function getCate()
    {
        return $this->hasOne(StyleCate::class, ['id'=>'style_cate_id'])->alias('cate');
    }
    /**
     * 关联款式渠道 一对一
     * @return \yii\db\ActiveQuery
     */
    public function getChannel()
    {
        return $this->hasOne(StyleChannel::class, ['id'=>'style_channel_id'])->alias('channel');
    }
    /**
     * 关联质检未过原因
     * @return \yii\db\ActiveQuery
     */
    public function getFqc()
    {
        return $this->hasOne(PurchaseFqcConfig::class, ['id'=>'iqc_reason'])->alias('fqc');
    }
}
