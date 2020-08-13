<?php

namespace addons\Supply\common\models;

use Yii;
use addons\Purchase\common\models\PurchaseGoods;
use addons\Style\common\enums\AttrIdEnum;
use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use common\models\backend\Member;

/**
 * This is the model class for table "supply_produce".
 *
 * @property int $id
 * @property int $merchant_id 商户ID
 * @property string $produce_sn 布产单编号
 * @property int $from_type 订单来源(1订单，2采购单)
 * @property string $purchase_sn 来源订单编号
 * @property int $order_detail_id 订单明细ID
 * @property string $qiban_sn 起版编号
 * @property int $qiban_type 起版类型
 * @property string $style_sn 款号
 * @property string $goods_name 商品名称
 * @property int $goods_num 商品数量
 * @property string $customer 顾客姓名
 * @property int $jintuo_type 金托类型
 * @property int $style_sex 款式性别
 * @property int $product_type_id 产品线
 * @property int $style_cate_id 款式分类
 * @property string $factory_mo 工厂模号
 * @property int $is_inlay 是否镶嵌
 * @property int $inlay_type 镶嵌方式
 * @property int $peiliao_type 配料类型
 * @property int $peishi_type 配石类型
 * @property int $peijian_type 配件类型
 * @property int $templet_type 样板类型
 * @property int $peiliao_status 配料状态
 * @property int $peishi_status 配石状态
 * @property int $peijian_status 配件状态
 * @property int $bc_status 布产状态 1初始化 2待确认 3待生产 4生产中 5待出厂 6部分出厂 7已出厂
 * @property int $prc_status 生产状态
 * @property int $follower_id 跟单人ID
 * @property int $follower_name 跟单人
 * @property int $created_at
 * @property int $updated_at
 * @property int $supplier_id 供应商ID
 * @property int $factory_order_time 工厂接单时间
 * @property int $factory_distribute_time 分配工厂时间
 * @property int $factory_delivery_time 工厂交货时间
 * @property int $standard_delivery_time 标准出厂时间
 * @property string $parts_info 配件信息
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
            [['merchant_id','is_inlay','peiliao_type','peishi_type','peijian_type','templet_type','peishi_status','peiliao_status','peijian_status', 'from_type', 'from_detail_id','apply_follower_id','audit_follower_status', 'qiban_type', 'goods_num', 'jintuo_type', 'style_sex', 'product_type_id', 'style_cate_id', 'bc_status', 'prc_status', 'follower_id', 'created_at', 'updated_at', 'supplier_id', 'factory_order_time', 'factory_distribute_time', 'factory_delivery_time', 'standard_delivery_time','purchase_detail_id','order_detail_id'], 'integer'],
            [['produce_sn', 'purchase_sn','order_sn', 'qiban_sn', 'style_sn'], 'string', 'max' => 30],
            [['goods_name','audit_followe_remark'], 'string', 'max' => 255],
            [['customer'], 'string', 'max' => 50],
            [['follower_name','factory_mo'], 'string', 'max' => 30],
            [['inlay_type'], 'string', 'max' => 10],
            [['parts_info'], 'safe'],
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
            'purchase_sn' => '采购单号',
            'order_sn' => '订单号',
            'purchase_detail_id' => '采购单明细ID',
            'order_detail_id' => '订单明细ID',
            'qiban_sn' => '起版编号',
            'qiban_type' => '起版类型',
            'style_sn' => '款式编号',
            'goods_name' => '商品名称',
            'goods_num' => '商品数量',
            'shippent_num' => '出厂数量',
            'factory_mo' => '工厂模号',
            'customer' => '顾客姓名',
            'jintuo_type' => '金托类型',
            'style_sex' => '款式性别',
            'product_type_id' => '产品线',
            'style_cate_id' => '款式分类',
            'is_inlay' => '是否镶嵌',
            'inlay_type' => '镶嵌方式',
            'peiliao_type' => '配料类型',
            'peishi_type' => '配石类型(公司)',
            'peijian_type' => '配件类型',
            'templet_type' => '样板类型',
            'peiliao_status' => '配料状态',
            'peishi_status' => '配石状态',
            'peijian_status' => '配件状态',
            'bc_status' => '布产状态',
            'prc_status' => '生产状态',
            'follower_id' => '跟单人id',
            'follower_name' => '跟单人',
            'apply_follower_id' => '跟单人（更改）',
            'audit_follower_status' => '跟单人审核',
            'audit_followe_remark' => '跟单人审核备注',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'supplier_id' => '供应商',
            'factory_order_time' => '工厂接单时间',
            'factory_distribute_time' => '分配工厂时间',
            'factory_delivery_time' => '工厂交货时间',
            'standard_delivery_time' => '标准出厂时间',
            'parts_info'=> '配件信息',
        ];
    }

    /**
     * 关联采购明细一对一
     * @return \yii\db\ActiveQuery
     */
    public function getPurchaseGoods()
    {
        return $this->hasOne(PurchaseGoods::class, ['id'=>'purchase_detail_id'])->alias('purchaseGoods');
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
     * 对应供应商模型
     * @return \yii\db\ActiveQuery
     */
    public function getSupplier()
    {
        return $this->hasOne(Supplier::class, ['id'=>'supplier_id']);
    }

    /**
     * 对应跟进人（管理员）模型
     * @return \yii\db\ActiveQuery
     */
    public function getFollower()
    {
        return $this->hasOne(Member::class, ['id'=>'follower_id'])->alias('follower');
    }
    /**
     * 布产单属性列表  一对多
     * @return \yii\db\ActiveQuery
     */
    public function getAttrs()
    {
        return $this->hasMany(ProduceAttribute::class, ['produce_id'=>'id'])->alias('attrs')->orderBy("sort asc");
    }
    /**
     * 配料列表  一对多
     * @return \yii\db\ActiveQuery
     */
    public function getProduceGolds()
    {
        return $this->hasMany(ProduceGold::class, ['produce_id'=>'id'])->alias('gold')->orderBy("id asc");
    }
    /**
     * 配石列表  一对多
     * @return \yii\db\ActiveQuery
     */
    public function getProduceStones()
    {
        return $this->hasMany(ProduceStone::class, ['produce_id'=>'id'])->alias('stone')->orderBy("id asc");
    }
    /**
     * 配件列表  一对多
     * @return \yii\db\ActiveQuery
     */
    public function getProduceParts()
    {
        return $this->hasMany(ProduceParts::class, ['produce_id'=>'id'])->alias('gold')->orderBy("id asc");
    }
}
