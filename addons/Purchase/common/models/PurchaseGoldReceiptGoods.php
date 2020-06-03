<?php

namespace addons\Purchase\common\models;

use Yii;

/**
 * This is the model class for table "purchase_gold_receipt_goods".
 *
 * @property int $id ID
 * @property int $receipt_id 采购收货单ID
 * @property string $purchase_sn 采购单编号
 * @property int $xuhao 序号
 * @property int $goods_status 收货单货品状态
 * @property string $goods_name 商品名称
 * @property int $goods_num 商品数量
 * @property int $material_type 商品类型
 * @property double $goods_weight 重量
 * @property string $cost_price 成本价
 * @property string $gold_price 金料价格/克
 * @property string $goods_remark 商品备注
 * @property int $put_in_type 入库方式
 * @property int $to_warehouse_id 入库仓库
 * @property int $sort 排序
 * @property int $status 状态 1启用 0禁用 -1 删除
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class PurchaseGoldReceiptGoods extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('purchase_gold_receipt_goods');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['receipt_id', 'purchase_sn'], 'required'],
            [['id', 'receipt_id', 'xuhao', 'goods_status', 'goods_num', 'material_type', 'put_in_type', 'to_warehouse_id', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['goods_weight', 'cost_price', 'gold_price'], 'number'],
            [['purchase_sn'], 'string', 'max' => 30],
            [['goods_name', 'goods_remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'receipt_id' => '采购收货单ID',
            'purchase_sn' => '采购单编号',
            'xuhao' => '序号',
            'goods_status' => '收货单货品状态',
            'goods_name' => '商品名称',
            'goods_num' => '商品数量',
            'material_type' => '商品类型',
            'goods_weight' => '重量',
            'cost_price' => '成本价',
            'gold_price' => '金料价格/克',
            'goods_remark' => '商品备注',
            'put_in_type' => '入库方式',
            'to_warehouse_id' => '入库仓库',
            'sort' => '排序',
            'status' => '状态 1启用 0禁用 -1 删除',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
