<?php

namespace addons\Gdzb\common\models;

use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use addons\Warehouse\common\models\Warehouse;
use common\models\backend\Member;
use Yii;

/**
 * This is the model class for table "gdzb_order_goods".
 *
 * @property int $id
 * @property int $order_id
 * @property string $goods_sn 商品编号
 * @property int $goods_status 货品状态
 * @property string $goods_size 尺寸(mm)
 * @property string $goods_image 商品图片
 * @property string $goods_name
 * @property string $cost_price 商品价格
 * @property int $warehouse_id 货品状态
 * @property string $goods_price 商品成交价
 * @property int $style_cate_id 商品分类
 * @property int $product_type_id 产品线
 * @property string $remark
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class Goods extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('goods');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'goods_status', 'warehouse_id', 'style_cate_id','creator_id', 'product_type_id', 'created_at', 'updated_at'], 'integer'],
            [['cost_price', 'goods_price'], 'number'],
            [['goods_sn', 'goods_size'], 'string', 'max' => 60],
            [['goods_name'], 'string', 'max' => 150],
            [['goods_image'], 'parseGoodsImage'],
            [['goods_sn'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'goods_sn' => '商品编号',
            'goods_status' => '货品状态',
            'goods_size' => '尺寸(mm)',
            'goods_image' => '商品图片',
            'goods_name' => '商品名称',
            'cost_price' => '成本价',
            'warehouse_id' => '所属仓库',
            'goods_price' => '实际销售价',
            'style_cate_id' => '商品分类',
            'product_type_id' => '产品线',
            'creator_id' => '创建人',
            'remark' => '备注',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * 款式图库
     */
    public function parseGoodsImage()
    {
        $goods_images = $this->goods_image;
        if(is_array($goods_images)){
            $this->goods_image = implode(',',$goods_images);
        }
        return $this->goods_image;
    }

    /**
     * 对应订单模型
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::class,['order_id'=>'id']);
    }


    /**
     * 关联产品线分类一对一
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(ProductType::class, ['id'=>'product_type_id'])->alias("type");
    }
    /**
     * 关联款式分类一对一
     * @return \yii\db\ActiveQuery
     */
    public function getCate()
    {
        return $this->hasOne(StyleCate::class, ['id'=>'style_cate_id'])->alias("cate");
    }

    /**
     * 关联仓库一对一
     * @return \yii\db\ActiveQuery
     */
    public function getWarehouse()
    {
        return $this->hasOne(Warehouse::class, ['id'=>'warehouse_id'])->alias("warehouse");
    }


    /**
     * 创建人
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(Member::class, ['id'=>'creator_id'])->alias('creator');
    }
}
