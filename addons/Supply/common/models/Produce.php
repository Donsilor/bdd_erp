<?php

namespace addons\Supply\common\models;

use addons\Purchase\common\models\PurchaseGoods;
use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use Yii;

/**
 * This is the model class for table "supply_produce".
 *
 * @property int $id
 * @property int $merchant_id 商户ID
 * @property string $produce_sn 布产单编号
 * @property int $from_type 订单来源(1订单，2采购单)
 * @property int $from_order_id 来源订单id
 * @property string $from_order_sn 来源订单编号
 * @property int $from_detail_id 订单明细ID
 * @property int $follower_id 跟单人ID
 * @property int $produce_status 生产状态 1待审核 2待分配 3待生产 4生产中 5待出厂 6部分出厂 7已出厂
 * @property int $qiban_type 起版类型 1有款起版 2无款起版 0 非起版
 * @property string $style_sn 款号
 * @property int $goods_num 商品数量
 * @property int $jintuo_type 金托类型
 * @property int $style_sex 款式性别
 * @property string $qiban_sn 起版编号
 * @property int $product_type_id 产品线
 * @property int $style_cate_id 款式分类
 * @property int $status 状态 -1删除 0禁用 1启用
 * @property int $created_at
 * @property int $updated_at
 */
class Produce extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('produce');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'from_type', 'from_order_id', 'from_detail_id', 'follower_id', 'produce_status', 'qiban_type', 'goods_num', 'jintuo_type', 'style_sex', 'product_type_id', 'style_cate_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['produce_sn', 'from_order_sn', 'style_sn', 'qiban_sn'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'merchant_id' => '商户ID',
            'produce_sn' => '布产单编号',
            'from_type' => '订单来源',
            'from_order_id' => '来源订单id',
            'from_order_sn' => '来源订单编号',
            'from_detail_id' => '订单明细ID',
            'follower_id' => '跟单人',
            'produce_status' => '生产状态',
            'qiban_type' => '起版类型',
            'style_sn' => '款号',
            'goods_num' => '商品数量',
            'jintuo_type' => '金托类型',
            'style_sex' => '款式性别',
            'qiban_sn' => '起版编号',
            'product_type_id' => '产品线',
            'style_cate_id' => '款式分类',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * 关联采购明细一对一
     * @return \yii\db\ActiveQuery
     */
    public function getPurchaseGoods()
    {
        return $this->hasOne(PurchaseGoods::class, ['id'=>'from_detail_id'])->alias('purchaseGoods');
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
     * 对应跟进人（管理员）模型
     * @return \yii\db\ActiveQuery
     */
    public function getFollower()
    {
        return $this->hasOne(\common\models\backend\Member::class, ['id'=>'follower_id']);
    }
}
