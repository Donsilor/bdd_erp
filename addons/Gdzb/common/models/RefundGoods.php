<?php

namespace addons\Gdzb\common\models;

use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use addons\Warehouse\common\models\Warehouse;
use Yii;

/**
 * This is the model class for table "gdzb_refund_goods".
 *
 * @property int $id
 * @property int $refund_id
 * @property int $order_goods_id 订单明细ID
 * @property string $goods_sn 商品编号
 * @property string $goods_size 尺寸(mm)
 * @property string $goods_image 商品图片
 * @property string $goods_name
 * @property string $cost_price 商品价格
 * @property string $refund_price
 * @property int $warehouse_id 所属仓库
 * @property int $style_cate_id 商品分类
 * @property int $product_type_id 产品线
 * @property int $creator_id
 * @property int $is_factory 是否返厂
 * @property string $remark
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class RefundGoods extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('refund_goods');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refund_id', 'order_goods_id', 'warehouse_id', 'style_cate_id', 'product_type_id', 'is_factory', 'created_at', 'updated_at'], 'integer'],
            [['cost_price','goods_price', 'refund_price'], 'number'],
            [['goods_sn'], 'string', 'max' => 60],
            [['goods_image', 'remark'], 'string', 'max' => 500],
            [['goods_name'], 'string', 'max' => 150],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'refund_id' => '退货ID',
            'order_goods_id' => '订单明细ID',
            'goods_sn' => '商品编号',
            'goods_price' => '商品价格',
            'goods_name' => '商品名称',
            'goods_image' => '商品图片',
            'cost_price' => '商品成本',
            'refund_price' => '退货金额',
            'warehouse_id' => '所属仓库',
            'style_cate_id' => '商品分类',
            'product_type_id' => '产品线',
            'is_factory' => '是否返厂',
            'remark' => '备注',
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
        return $this->hasOne(ProductType::class, ['id'=>'product_type_id'])->alias("productType");
    }
    /**
     * 关联款式分类一对一
     * @return \yii\db\ActiveQuery
     */
    public function getStyleCate()
    {
        return $this->hasOne(StyleCate::class, ['id'=>'style_cate_id'])->alias("styleCate");
    }

    /**
     * 关联仓库一对一
     * @return \yii\db\ActiveQuery
     */
    public function getWarehouse()
    {
        return $this->hasOne(Warehouse::class, ['id'=>'warehouse_id'])->alias("warehouse");
    }
}
