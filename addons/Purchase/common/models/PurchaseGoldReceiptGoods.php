<?php

namespace addons\Purchase\common\models;

use Yii;

/**
 * This is the model class for table "purchase_receipt_gold".
 *
 * @property int $id ID
 * @property int $receipt_id 采购收货单ID
 * @property string $purchase_sn 采购单编号
 * @property int $xuhao 序号
 * @property int $purchase_detail_id 采购单商品明细ID
 * @property int $goods_status 收货单货品状态
 * @property string $goods_name 商品名称
 * @property string $goods_sn 商品编号
 * @property int $goods_num 商品数量
 * @property string $material_type 商品类型
 * @property double $goods_weight 重量
 * @property string $cost_price 成本价
 * @property string $incl_tax_price 含税总额
 * @property string $gold_price 金料价格/克
 * @property string $goods_remark 商品备注
 * @property int $put_in_type 入库方式
 * @property int $to_warehouse_id 入库仓库
 * @property int $iqc_reason 质检未过原因
 * @property string $iqc_remark 质检备注
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
        return self::tableFullName('purchase_receipt_gold');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['receipt_id', 'purchase_sn'], 'required'],
            [['id', 'receipt_id', 'xuhao', 'purchase_detail_id', 'goods_status', 'goods_num', 'put_in_type', 'to_warehouse_id', 'iqc_reason', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['goods_weight', 'cost_price', 'gold_price', 'incl_tax_price'], 'number'],
            [['goods_sn', 'purchase_sn'], 'string', 'max' => 30],
            [['material_type'], 'string', 'max' => 10],
            [['goods_name', 'goods_remark', 'iqc_remark'], 'string', 'max' => 255],
            [['supplier_id', 'receipt_no'], 'safe'],
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
            'purchase_sn' => '采购单号',
            'xuhao' => '序号',
            'purchase_detail_id' => '采购单商品明细ID',
            'goods_status' => '货品状态',
            'goods_name' => '货品名称',
            'goods_sn' => '金料款号',
            'goods_num' => '货品数量',
            'material_type' => '金料材质',
            'goods_weight' => '金料总重/克',
            'cost_price' => '金料总额',
            'incl_tax_price' => '含税总额',
            'gold_price' => '金料单价/克',
            'goods_remark' => '备注',
            'put_in_type' => '入库方式',
            'to_warehouse_id' => '入库仓库',
            'iqc_reason' => '质检未过原因',
            'iqc_remark' => '质检备注',
            'sort' => '排序',
            'status' => '状态 1启用 0禁用 -1 删除',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
    /**
     * 关联采购收货单明细表
     * @return \yii\db\ActiveQuery
     */
    public function getReceipt(){
        return $this->hasOne(PurchaseReceipt::class, ['id'=>'receipt_id'])->alias('receipt');
    }
    /**
     * 关联质检未过原因
     * @return \yii\db\ActiveQuery
     */
    public function getFqc()
    {
        return $this->hasOne(PurchaseFqcConfig::class, ['id'=>'iqc_reason'])->alias('fqc');
    }
}
