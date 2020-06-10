<?php

namespace addons\Warehouse\common\models;

use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use Yii;

/**
 * This is the model class for table "warehouse_bill_goods_t".
 *
 * @property int $id
 * @property int $bill_id 单据ID
 * @property string $goods_id 库存货号
 * @property string $goods_name 商品名称
 * @property string $goods_image 商品图片
 * @property string $style_sn 款号
 * @property int $product_type_id 产品线
 * @property int $style_cate_id 款式分类
 * @property int $style_sex 款式性别
 * @property string $gold_weight 金重
 * @property string $gold_loss 金损
 * @property string $gross_weight 毛重
 * @property string $finger 手寸
 * @property string $cert_type 证书类别
 * @property string $cert_id 证书号
 * @property int $goods_num 商品数量
 * @property string $material 主成色
 * @property string $material_type 材质
 * @property string $material_color 材质颜色
 * @property string $diamond_carat 钻石大小
 * @property string $diamond_color 砖石颜色
 * @property string $diamond_shape 砖石形状
 * @property string $diamond_clarity 钻石净度
 * @property string $diamond_cut 钻石切工
 * @property string $diamond_polish 钻石抛光
 * @property string $diamond_symmetry 钻石对称
 * @property string $diamond_fluorescence 钻石荧光
 * @property string $diamond_discount 钻石折扣
 * @property string $diamond_cert_type 钻石证书类型
 * @property string $diamond_cert_id 钻石证书号
 * @property int $jintuo_type 金托类型
 * @property string $market_price 市场价(标签价)
 * @property string $cost_price 成本价
 * @property string $xiangkou 戒托镶口
 * @property string $length 长度
 * @property string $parts_gold_weight 配件金重
 * @property int $parts_num 配件数量
 * @property string $main_stone_type 主石类型
 * @property int $main_stone_num 主石粒数
 * @property string $main_stone_price 主石成本
 * @property string $second_stone_type1 副石1类型
 * @property int $second_stone_num1 副石1粒数
 * @property string $second_stone_weight1 副石1重
 * @property string $second_stone_price1 副石1总计价
 * @property string $second_stone_color1 副石1颜色
 * @property string $second_stone_clarity1 副石1净度
 * @property string $second_stone_shape1 副石1形状
 * @property string $second_stone_type2 副石2类型
 * @property int $second_stone_num2 副石2粒数
 * @property string $second_stone_weight2 副石2重
 * @property string $second_stone_price2 副石2总计价
 * @property string $remark 商品备注
 * @property int $creator_id 创建人
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class WarehouseBillGoodsT extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_bill_goods_t');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bill_id'], 'required'],
            [['id', 'bill_id', 'product_type_id', 'style_cate_id', 'style_sex', 'goods_num', 'jintuo_type', 'parts_num', 'main_stone_num', 'second_stone_num1', 'second_stone_num2', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['gold_weight', 'gold_loss', 'diamond_carat', 'market_price', 'cost_price', 'xiangkou', 'parts_gold_weight', 'main_stone_price', 'second_stone_weight1', 'second_stone_price1', 'second_stone_weight2', 'second_stone_price2'], 'number'],
            [['goods_id', 'style_sn'], 'string', 'max' => 30],
            [['goods_name', 'goods_image', 'cert_id', 'length'], 'string', 'max' => 100],
            [['gross_weight', 'diamond_cert_id'], 'string', 'max' => 20],
            [['finger', 'material', 'material_type', 'material_color', 'diamond_color', 'diamond_shape', 'diamond_clarity', 'diamond_cut', 'diamond_polish', 'diamond_symmetry', 'diamond_fluorescence', 'diamond_discount', 'diamond_cert_type', 'main_stone_type', 'second_stone_type1', 'second_stone_color1', 'second_stone_clarity1', 'second_stone_shape1', 'second_stone_type2'], 'string', 'max' => 10],
            [['cert_type'], 'string', 'max' => 50],
            [['remark'], 'string', 'max' => 255],
            [['goods_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bill_id' => '单据ID',
            'goods_id' => '库存货号',
            'goods_name' => '商品名称',
            'goods_image' => '商品图片',
            'style_sn' => '款号',
            'product_type_id' => '产品线',
            'style_cate_id' => '款式分类',
            'style_sex' => '款式性别',
            'gold_weight' => '金重',
            'gold_loss' => '金损',
            'gross_weight' => '毛重',
            'finger' => '手寸',
            'cert_type' => '证书类别',
            'cert_id' => '证书号',
            'goods_num' => '商品数量',
            'material' => '主成色',
            'material_type' => '材质',
            'material_color' => '材质颜色',
            'diamond_carat' => '钻石大小',
            'diamond_color' => '钻石颜色',
            'diamond_shape' => '钻石形状',
            'diamond_clarity' => '钻石净度',
            'diamond_cut' => '钻石切工',
            'diamond_polish' => '钻石抛光',
            'diamond_symmetry' => '钻石对称',
            'diamond_fluorescence' => '钻石荧光',
            'diamond_discount' => '钻石折扣',
            'diamond_cert_type' => '钻石证书类型',
            'diamond_cert_id' => '钻石证书号',
            'jintuo_type' => '金托类型',
            'market_price' => '市场价(标签价)',
            'cost_price' => '成本价',
            'xiangkou' => '戒托镶口',
            'length' => '长度',
            'parts_gold_weight' => '配件金重',
            'parts_num' => '配件数量',
            'main_stone_type' => '主石类型',
            'main_stone_num' => '主石粒数',
            'main_stone_price' => '主石成本',
            'second_stone_type1' => '副石1类型',
            'second_stone_num1' => '副石1粒数',
            'second_stone_weight1' => '副石1重',
            'second_stone_price1' => '副石1总计价',
            'second_stone_color1' => '副石1颜色',
            'second_stone_clarity1' => '副石1净度',
            'second_stone_shape1' => '副石1形状',
            'second_stone_type2' => '副石2类型',
            'second_stone_num2' => '副石2粒数',
            'second_stone_weight2' => '副石2重',
            'second_stone_price2' => '副石2总计价',
            'remark' => '商品备注',
            'creator_id' => '创建人',
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
        return $this->hasOne(ProductType::class, ['id'=>'product_type_id']);
    }

    /**
     * 关联款式分类一对一
     * @return \yii\db\ActiveQuery
     */
    public function getStyleCate()
    {
        return $this->hasOne(StyleCate::class, ['id'=>'style_cate_id']);
    }
}
