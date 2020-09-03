<?php

namespace addons\Gdzb\common\models;

use Yii;

/**
 * This is the model class for table "gdzb_order_goods".
 *
 * @property int $id
 * @property string $goods_sn 商品编号
 * @property string $goods_image 商品图片
 * @property string $goods_name
 * @property string $cost_price 商品价格
 * @property string $goods_price 商品成交价
 * @property string $style_sn 款式编号
 * @property int $style_cate_id 商品分类
 * @property int $product_type_id 产品线
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class OrderGoods extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('order_goods');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'style_cate_id', 'product_type_id', 'created_at', 'updated_at'], 'integer'],
            [['cost_price', 'goods_price'], 'number'],
            [['goods_sn'], 'string', 'max' => 60],
            [['goods_image'], 'string', 'max' => 500],
            [['goods_name'], 'string', 'max' => 150],
            [['style_sn'], 'string', 'max' => 30],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'goods_sn' => '商品编号',
            'goods_image' => '商品图片',
            'goods_name' => 'Goods Name',
            'cost_price' => '商品价格',
            'goods_price' => '商品成交价',
            'style_sn' => '款式编号',
            'style_cate_id' => '商品分类',
            'product_type_id' => '产品线',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
