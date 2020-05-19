<?php

namespace addons\Style\common\models;

use Yii;

/**
 * 款式商品 Model
 *
 * @property int $id 商品id(SKU)
 * @property int $merchant_id 商户ID
 * @property int $style_id 款式id
 * @property string $style_sn 款式编号
 * @property int $style_cate_id 款式分类id
 * @property int $product_type_id 产品线ID
 * @property string $goods_sn 商品编码
 * @property string $goods_image 商品主图
 * @property string $goods_name 商品名称
 * @property string $xiangkou 镶口
 * @property string $finger 指圈
 * @property int $material 材质
 * @property string $main_stone_weight 主石大小
 * @property int $main_stone_num 主石数量
 * @property string $second_stone_weight1 副石1重量
 * @property int $second_stone_num1 副石1数量
 * @property string $second_stone_weight2 副石2重
 * @property int $second_stone_num2 副石2数量
 * @property string $gold_price 金托成本
 * @property double $gold_weight 金托重量
 * @property double $gold_weight_diff 金托上下金差
 * @property double $finger_range 改圈范围
 * @property string $cost_price 成本价
 * @property int $goods_num 商品库存
 * @property string $spec_key 规格key(属性值ID逗号隔开)
 * @property string $goods_spec 规格属性(ID)
 * @property int $is_stock 是否现货 1现货 0期货
 * @property int $status 商品状态 0下架，1上架，-1已删除
 * @property int $created_at 商品添加时间
 * @property int $updated_at 商品编辑时间
 */
class StyleGoods extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('style_goods');
    }    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
                [['id','merchant_id', 'style_id', 'style_cate_id', 'product_type_id', 'material', 'main_stone_num', 'second_stone_num1', 'second_stone_num2', 'goods_num', 'is_stock', 'status', 'created_at', 'updated_at'], 'integer'],
                [['style_id'], 'required'],
                [['xiangkou', 'finger', 'main_stone_weight', 'second_stone_weight1', 'second_stone_weight2','gold_price','gold_weight', 'gold_weight_diff', 'finger_range', 'cost_price'], 'number'],
                [['style_sn'], 'string', 'max' => 30],
                [['goods_sn', 'spec_key'], 'string', 'max' => 50],
                [['goods_image'], 'string', 'max' => 100],
                [['goods_name'], 'string', 'max' => 500],
                [['goods_spec','remark'], 'string', 'max' => 255],
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
                'style_id' => '款式id',
                'style_sn' => '款式编号',
                'style_cate_id' => '款式分类id',
                'product_type_id' => '产品线ID',
                'goods_sn' => '商品编码',
                'goods_image' => '商品主图',
                'goods_name' => '商品名称',
                'xiangkou' => '镶口',
                'finger' => '指圈',
                'material' => '成色',
                'main_stone_weight' => '主石大小',
                'main_stone_num' => '主石数量',
                'second_stone_weight1' => '副石1重量',
                'second_stone_num1' => '副石1数量',
                'second_stone_weight2' => '副石2重',
                'second_stone_num2' => '副石2数量',                           
                'finger_range' => '改圈范围',
                'cost_price' => '成本价',
                'gold_price' => '金托成本',
                'gold_weight' => '金拖标准金重',
                'gold_weight_diff' => '金拖上下公差',
                'goods_num' => '商品数量',
                'spec_key' => '规格key',
                'goods_spec' => '规格属性',
                'remark' => '备注',
                'is_stock' => '是否现货',
                'status' => '商品状态',
                'created_at' => '商品添加时间',
                'updated_at' => '商品编辑时间',
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
     * 款式一对一
     * @return \yii\db\ActiveQuery
     */
    public function getStyle()
    {
        return $this->hasOne(Style::class, ['id'=>'style_id'])->alias('style');
    }
}
