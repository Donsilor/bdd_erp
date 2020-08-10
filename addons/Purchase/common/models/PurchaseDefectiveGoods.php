<?php

namespace addons\Purchase\common\models;

use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use Yii;

/**
 * This is the model class for table "purchase_defective_goods".
 *
 * @property int $id ID
 * @property int $defective_id 单据ID
 * @property int $xuhao 序号
 * @property string $goods_name 商品名称
 * @property int $goods_num 商品数量
 * @property string $style_sn 款式编号
 * @property string $factory_mo 工厂模号
 * @property int $style_cate_id 款式分类
 * @property int $product_type_id 产品线
 * @property int $style_channel_id 所属渠道
 * @property string $produce_sn 布产号
 * @property int $receipt_detail_id 收货单明细ID
 * @property string $material_type 商品类型
 * @property double $goods_weight 商品重量
 * @property string $goods_shape 商品形状
 * @property string $goods_color 颜色
 * @property string $goods_clarity 净度
 * @property string $goods_norms 商品规格
 * @property string $goods_size 商品尺寸
 * @property string $cost_price 总金额(成本价)
 * @property string $goods_price 商品单价/克/CT
 * @property string $parts_type 配件类型
 * @property string $chain_type 链类型
 * @property string $cramp_ring 扣环
 * @property int $iqc_reason 质检未过原因
 * @property string $iqc_remark 质检备注
 * @property int $sort 排序
 * @property int $status 状态 1启用 0禁用 -1 删除
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class PurchaseDefectiveGoods extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('purchase_defective_goods');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['defective_id', 'xuhao'], 'required'],
            [['defective_id', 'xuhao', 'goods_num', 'style_cate_id', 'product_type_id', 'style_channel_id', 'receipt_detail_id', 'iqc_reason', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['goods_weight', 'cost_price', 'goods_price'], 'number'],
            [['goods_name', 'iqc_remark'], 'string', 'max' => 255],
            [['style_sn'], 'string', 'max' => 50],
            [['factory_mo', 'produce_sn'], 'string', 'max' => 30],
            [['material_type', 'goods_norms'], 'string', 'max' => 20],
            [['goods_shape', 'goods_color', 'goods_clarity', 'parts_type', 'chain_type', 'cramp_ring'], 'string', 'max' => 10],
            [['goods_size'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'defective_id' => '单据ID',
            'xuhao' => '序号',
            'goods_name' => '商品名称',
            'goods_num' => '商品数量',
            'style_sn' => '款式编号',
            'factory_mo' => '工厂模号',
            'style_cate_id' => '款式分类',
            'product_type_id' => '产品线',
            'style_channel_id' => '所属渠道',
            'produce_sn' => '布产号',
            'receipt_detail_id' => '收货单明细ID',
            'material_type' => '商品类型',
            'goods_weight' => '商品重量(g)',
            'goods_shape' => '商品形状',
            'goods_color' => '颜色',
            'goods_clarity' => '净度',
            'goods_norms' => '商品规格',
            'goods_size' => '商品尺寸(mm)',
            'cost_price' => '总金额(成本价)',
            'goods_price' => '商品单价/克/CT',
            'parts_type' => '配件类型',
            'chain_type' => '链类型',
            'cramp_ring' => '扣环',
            'iqc_reason' => '质检未过原因',
            'iqc_remark' => '质检备注',
            'sort' => '排序',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
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
     * 款式分类一对一
     * @return \yii\db\ActiveQuery
     */
    public function getCate()
    {
        return $this->hasOne(StyleCate::class, ['id'=>'style_cate_id'])->alias('cate');
    }
    /**
     * 关联质检未过原因
     * @return \yii\db\ActiveQuery
     */
    public function getFqc()
    {
        return $this->hasOne(PurchaseFqcConfig::class, ['id'=>'iqc_reason'])->alias('fqc');
    }
    /**
     * 关联采购收货明细
     * @return \yii\db\ActiveQuery
     */
    public function getRecGoods()
    {
        return $this->hasOne(PurchaseReceiptGoods::class, ['id'=>'receipt_detail_id'])->alias('recGoods');
    }
}
