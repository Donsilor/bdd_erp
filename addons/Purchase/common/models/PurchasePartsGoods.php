<?php

namespace addons\Purchase\common\models;

use Yii;

/**
 * This is the model class for table "purchase_parts_goods".
 *
 * @property int $id ID
 * @property int $purchase_id 采购单ID
 * @property string $parts_type 配件类型
 * @property string $goods_sn 商品编号
 * @property string $goods_name 商品名称
 * @property int $goods_num 商品数量
 * @property double $goods_weight 商品重量(g)
 * @property string $goods_color 颜色
 * @property string $goods_shape 形状
 * @property string $goods_size 尺寸
 * @property string $chain_type 链类型
 * @property string $cramp_ring 扣环
 * @property string $material_type 金属材质
 * @property string $cost_price 成本价
 * @property string $incl_tax_price 含税总额
 * @property string $gold_price 金价/g
 * @property int $is_apply 是否申请修改
 * @property string $apply_info
 * @property int $is_receipt 是否收货
 * @property int $status 状态： -1已删除 0禁用 1启用
 * @property string $remark 采购备注
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class PurchasePartsGoods extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('purchase_parts_goods');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['purchase_id'], 'required'],
            [['purchase_id', 'goods_num', 'is_apply', 'is_receipt', 'status', 'created_at', 'updated_at'], 'integer'],
            [['goods_weight', 'cost_price', 'incl_tax_price', 'gold_price'], 'number'],
            [['apply_info'], 'string'],
            [['parts_type', 'goods_color', 'goods_shape', 'chain_type', 'cramp_ring', 'material_type'], 'string', 'max' => 10],
            [['goods_sn'], 'string', 'max' => 60],
            [['goods_name', 'remark'], 'string', 'max' => 255],
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
            'parts_type' => '配件类型',
            'goods_sn' => '商品编号',
            'goods_name' => '商品名称',
            'goods_num' => '商品数量',
            'goods_weight' => '商品重量(g)',
            'goods_color' => '颜色',
            'goods_shape' => '形状',
            'goods_size' => '尺寸(mm)',
            'chain_type' => '链类型',
            'cramp_ring' => '扣环',
            'material_type' => '金属材质',
            'cost_price' => '成本价',
            'incl_tax_price' => '含税总额',
            'gold_price' => '金价/g',
            'is_apply' => '是否申请修改',
            'apply_info' => 'Apply Info',
            'is_receipt' => '是否收货',
            'status' => '状态',
            'remark' => '采购备注',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
