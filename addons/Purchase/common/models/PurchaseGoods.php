<?php

namespace addons\Purchase\common\models;

use Yii;
use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;

/**
 * This is the model class for table "purchase_goods".
 *
 * @property int $id ID
 * @property int $purchase_id 采购单ID
 * @property int $goods_type 商品类型 1款号 2起版号
 * @property int $style_id 款号id
 * @property string $style_sn 款式编号
 * @property int $goods_name  商品名称
 * @property int $product_type_id 产品线
 * @property int $style_cate_id 款式分类
 * @property int $style_sex 款式性别
 * @property string $cost_price 成本价
 * @property int $goods_num 商品数量
 * @property int $produce_id 布产ID
 * @property int $status 状态： -1已删除 0禁用 1启用
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class PurchaseGoods extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('purchase_goods');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['style_sn','purchase_id', 'goods_type','goods_name','cost_price','product_type_id','style_cate_id','goods_num'], 'required'],
            [['purchase_id', 'goods_type', 'style_id', 'product_type_id', 'style_cate_id', 'style_sex', 'goods_num', 'produce_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['cost_price'], 'number'],
            [['style_sn'], 'string', 'max' => 30],
            [['goods_name','remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'purchase_id' => '采购单ID',
            'goods_type' => '商品类型',
            'style_id' => '款号id',
            'style_sn' => '款号/起版号',
            'goods_name' => '商品名称',
            'product_type_id' => '产品线',
            'style_cate_id' => '款式分类',
            'style_sex' => '款式性别',
            'cost_price' => '成本价',
            'goods_num' => '商品数量',
            'produce_id' => '布产ID',
            'remark' => '采购备注',
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
     * 采购单一对一
     * @return \yii\db\ActiveQuery
     */
    public function getPurchase()
    {
        return $this->hasOne(Purchase::class, ['id'=>'style_id'])->alias('style');
    }
}
