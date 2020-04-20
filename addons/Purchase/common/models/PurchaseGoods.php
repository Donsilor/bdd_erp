<?php

namespace addons\Purchase\common\models;

use Yii;

/**
 * This is the model class for table "purchase_goods".
 *
 * @property int $id ID
 * @property int $purchase_id 采购单ID
 * @property int $purchase_type 采购类型 1有款采购 2起版采购
 * @property int $style_id 款号id
 * @property string $style_sn 款式编号
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
            [['purchase_id', 'purchase_type'], 'required'],
            [['purchase_id', 'purchase_type', 'style_id', 'product_type_id', 'style_cate_id', 'style_sex', 'goods_num', 'produce_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['cost_price'], 'number'],
            [['style_sn'], 'string', 'max' => 30],
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
            'purchase_type' => '采购类型 1有款采购 2起版采购',
            'style_id' => '款号id',
            'style_sn' => '款式编号',
            'product_type_id' => '产品线',
            'style_cate_id' => '款式分类',
            'style_sex' => '款式性别',
            'cost_price' => '成本价',
            'goods_num' => '商品数量',
            'produce_id' => '布产ID',
            'status' => '状态： -1已删除 0禁用 1启用',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
