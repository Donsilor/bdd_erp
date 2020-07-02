<?php

namespace addons\Sales\common\models;

use Yii;

/**
 * This is the model class for table "sales_order_goods".
 *
 * @property int $id 订单商品表索引id
 * @property int $merchant_id 商户ID
 * @property int $order_id 订单id
 * @property string $style_sn 款式编号
 * @property string $goods_sn 商品编号
 * @property string $goods_id 现货货号
 * @property int $style_cate_id 款式分类
 * @property int $product_type_id 产品线
 * @property string $goods_name 商品名称
 * @property int $goods_num 商品数量
 * @property string $goods_image 商品图片
 * @property string $goods_price 商品价格
 * @property string $goods_pay_price 商品实际成交价
 * @property string $goods_discount 优惠金额
 * @property string $goods_spec 商品规格
 * @property string $currency 货币
 * @property double $exchange_rate 汇率
 * @property int $delivery_status 发货状态
 * @property int $distribute_status 配货状态
 * @property string $produce_sn 布产编号
 * @property int $is_stock 是否现货(1是0否)
 * @property int $is_gift 是否赠品
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class OrderGoods extends \addons\Sales\common\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sales_order_goods';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'order_id', 'style_cate_id', 'product_type_id', 'goods_num', 'delivery_status', 'distribute_status', 'is_stock', 'is_gift', 'created_at', 'updated_at'], 'integer'],
            [['order_id', 'goods_id'], 'required'],
            [['goods_price', 'goods_pay_price', 'goods_discount', 'exchange_rate'], 'number'],
            [['style_sn', 'goods_sn'], 'string', 'max' => 50],
            [['goods_id'], 'string', 'max' => 20],
            [['goods_name'], 'string', 'max' => 300],
            [['goods_image'], 'string', 'max' => 100],
            [['goods_spec'], 'string', 'max' => 255],
            [['currency'], 'string', 'max' => 5],
            [['produce_sn'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '订单商品表索引id',
            'merchant_id' => '商户ID',
            'order_id' => '订单id',
            'style_sn' => '款式编号',
            'goods_sn' => '商品编号',
            'goods_id' => '现货货号',
            'style_cate_id' => '款式分类',
            'product_type_id' => '产品线',
            'goods_name' => '商品名称',
            'goods_num' => '商品数量',
            'goods_image' => '商品图片',
            'goods_price' => '商品价格',
            'goods_pay_price' => '商品实际成交价',
            'goods_discount' => '优惠金额',
            'goods_spec' => '商品规格',
            'currency' => '货币',
            'exchange_rate' => '汇率',
            'delivery_status' => '发货状态',
            'distribute_status' => '配货状态',
            'produce_sn' => '布产编号',
            'is_stock' => '是否现货(1是0否)',
            'is_gift' => '是否赠品',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
