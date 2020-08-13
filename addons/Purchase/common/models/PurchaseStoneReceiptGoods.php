<?php

namespace addons\Purchase\common\models;

use addons\Sales\common\models\SaleChannel;
use Yii;

/**
 * This is the model class for table "purchase_receipt_stone".
 *
 * @property int $id ID
 * @property int $receipt_id 采购收货单ID
 * @property string $purchase_sn 采购单编号
 * @property int $purchase_detail_id 采购单商品明细ID
 * @property int $goods_status 收货单货品状态
 * @property string $goods_name 商品名称
 * @property string $goods_sn 石料款号
 * @property int $goods_num 商品数量
 * @property int $stone_num 石料粒数
 * @property string $stone_weight 单颗石重
 * @property string $material_type 商品类型
 * @property double $goods_weight 重量
 * @property string $goods_shape 形状
 * @property string $goods_color 颜色
 * @property string $goods_clarity 净度
 * @property string $goods_cut 切工
 * @property string $goods_symmetry 对称
 * @property string $goods_polish 抛光
 * @property string $goods_fluorescence 荧光
 * @property string $goods_colour 色彩
 * @property string $cert_type 证书类型
 * @property string $cert_id 证书号
 * @property string $goods_norms 规格
 * @property string $goods_size 尺寸
 * @property string $cost_price 成本价
 * @property string $stone_price 石料单价/CT
 * @property string $goods_remark 商品备注
 * @property int $channel_id 渠道
 * @property int $put_in_type 入库方式
 * @property int $to_warehouse_id 入库仓库
 * @property int $iqc_reason 质检未过原因
 * @property string $iqc_remark 质检备注
 * @property int $xuhao 序号
 * @property int $sort 排序
 * @property int $status 状态 1启用 0禁用 -1 删除
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class PurchaseStoneReceiptGoods extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('purchase_receipt_stone');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['receipt_id', 'purchase_sn'], 'required'],
            [['id', 'receipt_id', 'goods_num', 'stone_num', 'channel_id', 'put_in_type', 'to_warehouse_id', 'purchase_detail_id', 'goods_status', 'iqc_reason', 'xuhao', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['goods_weight', 'stone_weight', 'cost_price', 'stone_price'], 'number'],
            [['purchase_sn', 'cert_id'], 'string', 'max' => 30],
            [['goods_name', 'goods_norms', 'goods_remark', 'iqc_remark'], 'string', 'max' => 255],
            [['goods_sn'], 'string', 'max' => 60],
            [['goods_size'], 'string', 'max' => 100],
            [['material_type', 'goods_shape', 'goods_color', 'goods_clarity', 'goods_cut', 'goods_symmetry', 'goods_polish', 'goods_fluorescence', 'goods_colour', 'cert_type'], 'string', 'max' => 10],
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
            'purchase_sn' => '采购单号',
            'purchase_detail_id' => '采购单明细ID',
            'goods_status' => '石料状态',
            'goods_name' => '石料名称',
            'goods_sn' => '石料款号',
            'goods_num' => '石料数量',
            'stone_num' => '石料粒数',
            'stone_weight' => '单颗石重(ct)',
            'material_type' => '石料类型',
            'goods_weight' => '石料总重(ct)',
            'goods_shape' => '形状',
            'goods_color' => '颜色',
            'goods_clarity' => '净度',
            'goods_cut' => '切工',
            'goods_symmetry' => '对称',
            'goods_polish' => '抛光',
            'goods_fluorescence' => '荧光',
            'goods_colour' => '色彩',
            'goods_norms' => '规格',
            'goods_size' => '尺寸(mm)',
            'cert_type' => '证书类型',
            'cert_id' => '证书号',
            'cost_price' => '石料总额',
            'stone_price' => '石料单价/ct',
            'goods_remark' => '商品备注',
            'put_in_type' => '入库方式',
            'to_warehouse_id' => '入库仓库',
            'iqc_reason' => '质检未过原因',
            'iqc_remark' => '质检备注',
            'channel_id' => '渠道',
            'xuhao' => '石料序号',
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
