<?php

namespace addons\Purchase\common\models;

use Yii;

/**
 * This is the model class for table "purchase_gold_goods".
 *
 * @property int $id ID
 * @property int $purchase_id 采购单ID
 * @property string $goods_sn 商品编号
 * @property string $goods_name 商品名称
 * @property int $goods_num 商品数量
 * @property double $goods_weight --商品包重量
 * @property string $material_type 商品类型
 * @property string $cost_price 成本价
 * @property string $gold_price 金料价格/克
 * @property int $is_apply 是否申请修改
 * @property string $apply_info
 * @property int $status 状态： -1已删除 0禁用 1启用
 * @property string $remark 采购备注
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class PurchaseGoldGoods extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('purchase_gold_goods');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['purchase_id'], 'required'],
            [['purchase_id', 'goods_num', 'is_apply', 'status', 'created_at', 'updated_at'], 'integer'],
            [['goods_weight', 'cost_price', 'gold_price'], 'number'],
            [['apply_info'], 'string'],
            [['goods_sn'], 'string', 'max' => 60],
            [['goods_name', 'remark'], 'string', 'max' => 255],
            [['material_type'], 'string', 'max' => 10],
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
            'goods_sn' => '商品编号',
            'goods_name' => '商品名称',
            'goods_num' => '商品数量',
            'goods_weight' => '--商品包重量',
            'material_type' => '商品类型',
            'cost_price' => '成本价',
            'gold_price' => '金料价格/克',
            'is_apply' => '是否申请修改',
            'apply_info' => 'Apply Info',
            'status' => '状态： -1已删除 0禁用 1启用',
            'remark' => '采购备注',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
