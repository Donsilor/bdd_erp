<?php

namespace addons\Purchase\common\models;

use Yii;
use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;

/**
 * This is the model class for table "purchase_receipt_gift".
 *
 * @property int $id ID
 * @property int $receipt_id 采购收货单ID
 * @property string $purchase_sn 采购单编号
 * @property string $goods_name 商品名称
 * @property string $goods_sn 商品编号
 * @property int $goods_num 商品数量
 * @property int $product_type_id 产品线
 * @property int $style_cate_id 款式分类
 * @property int $style_sex 款式性别
 * @property string $material_type 材质
 * @property string $material_color 材质颜色
 * @property string $finger 手寸(美)
 * @property string $finger_hk 手寸(港)
 * @property string $chain_length 链长
 * @property string $main_stone_type 主石类型
 * @property int $main_stone_num 主石数量
 * @property string $goods_size 商品尺寸
 * @property double $goods_weight 商品重量(g)
 * @property string $cost_price 成本价
 * @property string $gold_price 金价/g
 * @property string $goods_remark 商品备注
 * @property int $put_in_type 入库方式
 * @property int $to_warehouse_id 入库仓库
 * @property int $xuhao 序号
 * @property int $goods_status 收货单货品状态
 * @property int $purchase_detail_id 采购单商品明细ID
 * @property int $iqc_reason 质检未过原因
 * @property string $iqc_remark 质检备注
 * @property int $sort 排序
 * @property int $status 状态 1启用 0禁用 -1 删除
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class PurchaseGiftReceiptGoods extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('purchase_receipt_gift');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['receipt_id', 'purchase_sn', 'goods_sn'], 'required'],
            [['receipt_id', 'goods_num', 'product_type_id', 'style_cate_id', 'style_sex', 'main_stone_num', 'put_in_type', 'to_warehouse_id', 'xuhao', 'goods_status', 'purchase_detail_id', 'iqc_reason', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['goods_weight', 'cost_price', 'gold_price'], 'number'],
            [['purchase_sn'], 'string', 'max' => 30],
            [['goods_name', 'goods_remark', 'iqc_remark'], 'string', 'max' => 255],
            [['goods_sn', 'chain_length'], 'string', 'max' => 60],
            [['material_type', 'material_color', 'finger', 'finger_hk', 'main_stone_type'], 'string', 'max' => 10],
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
            'receipt_id' => '采购收货单ID',
            'purchase_sn' => '采购单编号',
            'goods_name' => '商品名称',
            'goods_sn' => '款式编号',
            'goods_num' => '商品数量',
            'product_type_id' => '产品线',
            'style_cate_id' => '款式分类',
            'style_sex' => '款式性别',
            'material_type' => '材质',
            'material_color' => '材质颜色',
            'finger' => '手寸(美号)',
            'finger_hk' => '手寸(港号)',
            'chain_length' => '链长(cm)',
            'main_stone_type' => '主石类型',
            'main_stone_num' => '主石数量',
            'goods_size' => '商品尺寸(mm)',
            'goods_weight' => '商品重量(g)',
            'cost_price' => '成本价',
            'gold_price' => '金价/g',
            'goods_remark' => '商品备注',
            'put_in_type' => '入库方式',
            'to_warehouse_id' => '入库仓库',
            'xuhao' => '序号',
            'goods_status' => '收货单货品状态',
            'purchase_detail_id' => '来源明细ID',
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
}
