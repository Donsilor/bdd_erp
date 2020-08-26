<?php

namespace addons\Purchase\common\models;

use addons\Style\common\models\ProductType;
use addons\Style\common\models\StyleCate;
use Yii;

/**
 * This is the model class for table "purchase_gift_goods".
 *
 * @property int $id ID
 * @property int $purchase_id 采购单ID
 * @property string $goods_image 图片
 * @property string $goods_sn 商品编号
 * @property string $goods_name 商品名称
 * @property int $goods_num 商品数量
 * @property double $goods_weight 商品重量(g)
 * @property int $product_type_id 产品线
 * @property int $style_cate_id 款式分类
 * @property int $style_sex 款式性别
 * @property string $finger 手寸(美)
 * @property string $finger_hk 手寸(港)
 * @property string $chain_length 链长
 * @property string $material_type 材质
 * @property string $material_color 材质颜色
 * @property string $main_stone_type 主石类型
 * @property int $main_stone_num 主石数量
 * @property string $goods_size 尺寸
 * @property string $gold_price 金价/g
 * @property string $cost_price 成本价
 * @property string $incl_tax_price 含税总额
 * @property int $is_apply 是否申请修改
 * @property string $apply_info
 * @property int $is_receipt 是否收货
 * @property int $status 状态： -1已删除 0禁用 1启用
 * @property string $remark 采购备注
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class PurchaseGiftGoods extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('purchase_gift_goods');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['purchase_id', 'goods_sn'], 'required'],
            [['purchase_id', 'goods_num', 'product_type_id', 'style_cate_id', 'style_sex', 'main_stone_num', 'is_apply', 'is_receipt', 'status', 'created_at', 'updated_at'], 'integer'],
            [['goods_weight', 'gold_price', 'cost_price', 'incl_tax_price'], 'number'],
            [['apply_info'], 'string'],
            [['goods_image', 'goods_name', 'remark'], 'string', 'max' => 255],
            [['goods_sn'], 'string', 'max' => 60],
            [['finger', 'finger_hk', 'chain_length', 'material_type', 'material_color', 'main_stone_type'], 'string', 'max' => 10],
            [['goods_size'], 'string', 'max' => 100],
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
            'goods_image' => '图片',
            'goods_sn' => '款式编号',
            'goods_name' => '商品名称',
            'goods_num' => '商品数量',
            'goods_weight' => '商品重量(g)',
            'product_type_id' => '产品线',
            'style_cate_id' => '款式分类',
            'style_sex' => '款式性别',
            'finger_hk' => '手寸(港号)',
            'finger' => '手寸(美号)',
            'chain_length' => '链长(cm)',
            'material_type' => '材质',
            'material_color' => '材质颜色',
            'main_stone_type' => '主石类型',
            'main_stone_num' => '主石数量',
            'goods_size' => '尺寸(mm)',
            'gold_price' => '金价/g',
            'cost_price' => '成本价',
            'incl_tax_price' => '含税总额',
            'is_apply' => '是否申请修改',
            'apply_info' => 'Apply Info',
            'is_receipt' => '是否收货',
            'status' => '状态',
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
}
