<?php

namespace addons\Purchase\common\models;

use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use Yii;

/**
 * This is the model class for table "purchase_defective_goods".
 *
 * @property int $id ID
 * @property int $defective_id 返厂单ID
 * @property int $xuhao 序号
 * @property string $goods_name 商品名称
 * @property int $goods_num 商品数量
 * @property string $style_sn 款式编号
 * @property string $factory_mo 工厂模号
 * @property int $style_cate_id 款式分类
 * @property int $product_type_id 产品线
 * @property string $produce_sn 布产号
 * @property int $material_type 商品类型
 * @property double $goods_weight 商品重量
 * @property string $goods_color 颜色
 * @property string $goods_clarity 净度
 * @property string $goods_norms 商品规格
 * @property string $cost_price 总金额(成本价)
 * @property string $goods_price 商品单价/克/CT
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
            [['id', 'defective_id', 'xuhao', 'goods_num', 'style_cate_id', 'product_type_id', 'material_type', 'iqc_reason', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['goods_weight', 'cost_price', 'goods_price'], 'number'],
            [['goods_name', 'iqc_remark'], 'string', 'max' => 255],
            [['style_sn'], 'string', 'max' => 50],
            [['factory_mo', 'produce_sn'], 'string', 'max' => 30],
            [['goods_color', 'goods_clarity'], 'string', 'max' => 10],
            [['goods_norms'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'defective_id' => '返厂单ID',
            'xuhao' => '序号',
            'goods_name' => '商品名称',
            'goods_num' => '商品数量',
            'style_sn' => '款式编号',
            'factory_mo' => '工厂模号',
            'style_cate_id' => '款式分类',
            'product_type_id' => '产品线',
            'produce_sn' => '布产号',
            'material_type' => '商品类型',
            'goods_weight' => '商品重量',
            'goods_color' => '颜色',
            'goods_clarity' => '净度',
            'goods_norms' => '商品规格',
            'cost_price' => '总金额(成本价)',
            'goods_price' => '商品单价/克/CT',
            'iqc_reason' => '质检未过原因',
            'iqc_remark' => '质检备注',
            'sort' => '排序',
            'status' => '状态 1启用 0禁用 -1 删除',
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
}
