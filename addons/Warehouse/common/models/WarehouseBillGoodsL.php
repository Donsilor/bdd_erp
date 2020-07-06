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
 * @property int $order_detail_id 订单明细ID
 * @property string $order_sn 订单号
 * @property string $produce_sn 布产单号
 * @property int $is_wholesale 是否批发
 * @property string $gold_weight 金重
 * @property string $gold_loss 金损
 * @property string $suttle_weight 净重
 * @property string $gold_price 金价
 * @property string $gold_amount 金料额
 * @property string $gross_weight 毛重
 * @property string $finger 手寸
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
 * @property string $bukou_fee 补口费
 * @property string $xianqian_fee 镶石费
 * @property string $cert_fee 证书费
 * @property string $markup_rate 倍率
 * @property string $fense_fee 分色/分件费
 * @property string $biaomiangongyi_fee 表面工艺费
 * @property string $xiangkou 戒托镶口
 * @property string $length 长度
 * @property string $parts_gold_weight 配件金重
 * @property string $parts_price 配件金额
 * @property string $parts_fee 配件工费
 * @property int $parts_num 配件数量
 * @property string $goods_color 货品外部颜色
 * @property string $main_stone_sn 主石编号
 * @property string $main_stone_type 主石类型
 * @property int $main_stone_num 主石粒数
 * @property string $main_stone_price 主石成本
 * @property string $second_stone_sn1 副石1编号
 * @property string $second_cert_id1 副石1证书号
 * @property string $second_stone_type1 副石1类型
 * @property int $second_stone_num1 副石1粒数
 * @property string $second_stone_weight1 副石1重
 * @property string $second_stone_price1 副石1总计价
 * @property string $second_stone_color1 副石1颜色
 * @property string $second_stone_clarity1 副石1净度
 * @property string $second_stone_shape1 副石1形状
 * @property string $second_stone_type2 副石2类型
 * @property int $second_stone_num2 副石2粒数
 * @property string $second_stone_weight2 副石2重
 * @property string $second_stone_price2 副石2总计价
 * @property int $source_detail_id 来源明细ID
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
            [['bill_id', 'product_type_id', 'style_cate_id', 'style_sex', 'style_channel_id', 'style_id', 'qiban_type', 'order_detail_id', 'is_wholesale', 'goods_num', 'jintuo_type', 'parts_num', 'main_stone_num', 'second_stone_num1', 'second_stone_num2', 'source_detail_id', 'status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['gold_weight', 'gold_loss', 'suttle_weight', 'gold_price', 'gold_amount', 'diamond_carat', 'market_price', 'cost_price', 'gong_fee', 'bukou_fee', 'xianqian_fee', 'cert_fee', 'markup_rate', 'fense_fee', 'biaomiangongyi_fee', 'parts_gold_weight', 'parts_price', 'parts_fee', 'main_stone_price', 'second_stone_weight1', 'second_stone_price1', 'second_stone_weight2', 'second_stone_price2'], 'number'],
            [['bill_no', 'goods_id', 'goods_sn', 'style_sn', 'qiban_sn', 'produce_sn'], 'string', 'max' => 30],
            [['bill_type'], 'string', 'max' => 3],
            [['goods_name', 'goods_image', 'product_size', 'cert_id', 'length', 'goods_color'], 'string', 'max' => 100],
            [['order_sn'], 'string', 'max' => 40],
            [['gross_weight', 'diamond_cert_id', 'main_stone_sn', 'second_stone_sn1', 'second_cert_id1'], 'string', 'max' => 20],
            [['finger', 'material', 'material_type', 'material_color', 'diamond_color', 'diamond_shape', 'diamond_clarity', 'diamond_cut', 'diamond_polish', 'diamond_symmetry', 'diamond_fluorescence', 'diamond_discount', 'diamond_cert_type', 'xiangkou', 'main_stone_type', 'second_stone_type1', 'second_stone_color1', 'second_stone_clarity1', 'second_stone_shape1', 'second_stone_type2'], 'string', 'max' => 10],
            [['cert_type', 'kezi'], 'string', 'max' => 50],
            [['remark'], 'string', 'max' => 255],
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
            'goods_id' => '货号',
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
            'order_detail_id' => '订单明细ID',
            'order_sn' => '订单号',
            'produce_sn' => '布产单号',
            'is_wholesale' => '是否批发',
            'gold_weight' => '金重',
            'gold_loss' => '金损',
            'suttle_weight' => '净重',
            'gold_price' => '金价',
            'gold_amount' => '金料额',
            'gross_weight' => '毛重',
            'finger' => '手寸',
            'product_size' => '尺寸',
            'kezi' => '刻字',
            'cert_type' => '证书类别',
            'cert_id' => '证书号',
            'goods_num' => '商品数量',
            'material' => '主成色',
            'material_type' => '材质',
            'material_color' => '材质颜色',
            'diamond_carat' => '钻石大小',
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
            'market_price' => '市场价(标签价)',
            'cost_price' => '成本价',
            'gong_fee' => '工费',
            'bukou_fee' => '补口费',
            'xianqian_fee' => '镶石费',
            'cert_fee' => '证书费',
            'markup_rate' => '倍率',
            'fense_fee' => '分色/分件费',
            'biaomiangongyi_fee' => '表面工艺费',
            'xiangkou' => '戒托镶口',
            'length' => '长度',
            'parts_gold_weight' => '配件金重',
            'parts_price' => '配件金额',
            'parts_fee' => '配件工费',
            'parts_num' => '配件数量',
            'goods_color' => '货品外部颜色',
            'main_stone_sn' => '主石编号',
            'main_stone_type' => '主石类型',
            'main_stone_num' => '主石粒数',
            'main_stone_price' => '主石成本',
            'second_stone_sn1' => '副石1编号',
            'second_cert_id1' => '副石1证书号',
            'second_stone_type1' => '副石1类型',
            'second_stone_num1' => '副石1粒数',
            'second_stone_weight1' => '副石1重',
            'second_stone_price1' => '副石1总计价',
            'second_stone_color1' => '副石1颜色',
            'second_stone_clarity1' => '副石1净度',
            'second_stone_shape1' => '副石1形状',
            'second_stone_type2' => '副石2类型',
            'second_stone_num2' => '副石2粒数',
            'second_stone_weight2' => '副石2重',
            'second_stone_price2' => '副石2总计价',
            'source_detail_id' => '来源明细ID',
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
