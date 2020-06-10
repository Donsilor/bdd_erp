<?php

namespace addons\Purchase\common\models;

use Yii;
use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use addons\Style\common\models\StyleChannel;

/**
 * This is the model class for table "purchase_apply_goods".
 *
 * @property int $id ID
 * @property int $apply_id 采购单ID
 * @property string $goods_sn 款号/起版号
 * @property int $goods_num 商品数量
 * @property string $goods_name 商品名称
 * @property string $style_id 款号/起版id
 * @property string $style_sn 款号
 * @property string $qiban_sn
 * @property int $qiban_type 起版类型 0非起版 1有款起版 2无款起版 
 * @property int $style_cate_id 款式分类
 * @property int $product_type_id 产品线
 * @property int $style_channel_id 归属渠道
 * @property int $style_sex 款式性别
 * @property int $jintuo_type 金托类型
 * @property int $is_inlay 是否镶嵌
 * @property string $cost_price 成本价
 * @property int $is_apply 是否申请修改
 * @property string $apply_info 申请修改数据
 * @property int $status 状态： -1已删除 0禁用 1启用
 * @property string $stone_info 石料信息
 * @property string $parts_info 配件信息
 * @property string $remark 采购备注
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class PurchaseApplyGoods extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('purchase_apply_goods');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['apply_id','goods_sn'], 'required'],
            [['style_id','apply_id', 'goods_num', 'qiban_type', 'style_cate_id', 'product_type_id', 'style_channel_id', 'style_sex', 'jintuo_type', 'is_inlay', 'is_apply', 'status', 'created_at', 'updated_at'], 'integer'],
            [['cost_price'], 'number'],
            [['apply_info'], 'string'],
            [['goods_sn'], 'string', 'max' => 60],
            [['goods_name', 'stone_info', 'parts_info', 'remark'], 'string', 'max' => 255],
            [['style_sn', 'qiban_sn'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'apply_id' => '采购单ID',
            'goods_sn' => '款号/起版号',
            'goods_num' => '商品数量',
            'goods_name' => '商品名称',
            'style_sn' => '款号',
            'qiban_sn' => '起版号',
            'qiban_type' => '起版类型 ',
            'style_cate_id' => '款式分类',
            'product_type_id' => '产品线',
            'style_channel_id' => '归属渠道',
            'style_sex' => '款式性别',
            'jintuo_type' => '金托类型',
            'is_inlay' => '是否镶嵌',
            'cost_price' => '成本价',
            'is_apply' => '是否申请修改',
            'apply_info' => '修改数据',
            'status' => '状态',
            'stone_info' => '石料信息',
            'parts_info' => '配件信息',
            'remark' => '采购备注',
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
     * 采购单一对一
     * @return \yii\db\ActiveQuery
     */
    public function getApply()
    {
        return $this->hasOne(PurchaseApply::class, ['id'=>'apply_id'])->alias('apply');
    }    
    /**
     * 款式渠道 一对一
     * @return \yii\db\ActiveQuery
     */
    public function getChannel()
    {
        return $this->hasOne(StyleChannel::class, ['id'=>'style_channel_id'])->alias('channel');
    }
    
    /**
     * 商品属性列表
     * @return \yii\db\ActiveQuery
     */
    public function getAttrs()
    {
        return $this->hasMany(PurchaseApplyGoodsAttribute::class, ['id'=>'id'])->alias('attrs');
    }
    
}
