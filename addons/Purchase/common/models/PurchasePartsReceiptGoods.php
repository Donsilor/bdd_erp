<?php

namespace addons\Purchase\common\models;

use addons\Sales\common\models\SaleChannel;
use Yii;

/**
 * This is the model class for table "purchase_receipt_parts".
 *
 * @property int $id ID
 * @property int $receipt_id 采购收货单ID
 * @property string $purchase_sn 采购单编号
 * @property string $goods_name 商品名称
 * @property string $goods_sn 商品编号
 * @property int $goods_num 商品数量
 * @property string $parts_type 配件类型
 * @property string $material_type 金属材质
 * @property string $goods_color 商品颜色
 * @property string $goods_shape 商品形状
 * @property string $goods_size 商品尺寸
 * @property double $goods_weight 重量
 * @property string $chain_type 链类型
 * @property string $cramp_ring 扣环
 * @property string $cost_price 成本价
 * @property string $parts_price 配件单价/克
 * @property string $goods_remark 商品备注
 * @property int $put_in_type 入库方式
 * @property int $to_warehouse_id 入库仓库
 * @property int $xuhao 序号
 * @property int $goods_status 收货单货品状态
 * @property int $purchase_detail_id 采购单商品明细ID
 * @property int $iqc_reason 质检未过原因
 * @property string $iqc_remark 质检备注
 * @property int $sort 排序
 * @property int $status 状态 1启用 0禁用 -1 删除
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class PurchasePartsReceiptGoods extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('purchase_receipt_parts');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['receipt_id', 'purchase_sn'], 'required'],
            [['id', 'receipt_id', 'goods_num', 'put_in_type', 'to_warehouse_id', 'xuhao', 'goods_status', 'purchase_detail_id', 'iqc_reason', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['goods_weight', 'cost_price', 'parts_price'], 'number'],
            [['purchase_sn'], 'string', 'max' => 30],
            [['goods_name', 'goods_remark', 'iqc_remark'], 'string', 'max' => 255],
            [['goods_sn'], 'string', 'max' => 60],
            [['parts_type', 'material_type', 'goods_color', 'goods_shape', 'chain_type', 'cramp_ring'], 'string', 'max' => 10],
            [['goods_size'], 'string', 'max' => 100],
            [['supplier_id','receipt_no','receipt_status'], 'safe']
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
            'goods_name' => '商品名称',
            'goods_sn' => '商品编号',
            'goods_num' => '商品数量',
            'parts_type' => '配件类型',
            'material_type' => '金属材质',
            'goods_color' => '商品颜色',
            'goods_shape' => '商品形状',
            'goods_size' => '商品尺寸(mm)',
            'goods_weight' => '重量(g)',
            'chain_type' => '链类型',
            'cramp_ring' => '扣环',
            'cost_price' => '成本价',
            'parts_price' => '配件单价/g',
            'goods_remark' => '商品备注',
            'put_in_type' => '入库方式',
            'to_warehouse_id' => '入库仓库',
            'xuhao' => '序号',
            'goods_status' => '收货单货品状态',
            'purchase_detail_id' => '采购单商品明细ID',
            'iqc_reason' => '质检未过原因',
            'iqc_remark' => '质检备注',
            'sort' => '排序',
            'status' => '状态',
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
    /**
     * 对应渠道模型
     * @return \yii\db\ActiveQuery
     */
    public function getSaleChannel()
    {
        return $this->hasOne(SaleChannel::class, ['id'=>'channel_id']);
    }
}
