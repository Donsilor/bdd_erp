<?php

namespace addons\Style\common\models;

use Yii;

/**
 * 款式商品
 *
 * @property int $id 商品id(SKU)
 * @property int $style_id 款式id
 * @property string $goods_sn 商品编号
 * @property int $goods_type 商品类型
 * @property string $goods_image 商品主图
 * @property int $merchant_id 商户ID
 * @property int $type_id 产品线id
 * @property string $sale_price 商品价格
 * @property string $market_price 市场价
 * @property string $cost_price 成本价
 * @property int $goods_num 商品库存
 * @property int $status 商品状态 0下架，1上架，10违规（禁售）
 * @property int $created_at 商品添加时间
 * @property int $updated_at 商品编辑时间
 * @property string $spec_key 规格值唯一key(规格值ID逗号隔开的字符串)
 */
class StyleGoods extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName("style_goods");
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['style_id', 'product_type_id','style_cate_id','status','id'], 'required'],
            [['style_id', 'product_type_id','style_cate_id', 'merchant_id','sale_volume', 'status', 'created_at', 'updated_at'], 'integer'],
            [['sale_price', 'market_price', 'cost_price',''], 'number'],
            ['sale_price','compare','compareValue' => 0, 'operator' => '>'],
            ['market_price','compare','compareValue' => 0, 'operator' => '>'],
            ['cost_price','compare','compareValue' => 0, 'operator' => '>'],
            [['goods_sn','style_sn'], 'string', 'max' => 50],
            ['goods_name', 'string', 'max' => 500],
            [['goods_image','spec_key'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'merchant_id' =>'商户',
            'style_id' => '款号',
            'style_sn' => '款式编号',
            'goods_sn' => '商品编号',
            'goods_image' => '商品图片',
            'goods_name' => '商品名称',
            'style_cate_id' => "款式分类",            
            'product_type_id' => "产品线",
            'cost_price' => "成本价",
            'sale_price' => "销售价",
            'market_price' => "市场价",
            'goods_num' => "商品数量",
            'sale_volume' => "商品销量",
            'status' => "状态",           
            'created_at' => "创建时间",
            'updated_at' => "更新时间",
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
