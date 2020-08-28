<?php

namespace addons\Purchase\common\models;

use Yii;
use addons\Supply\common\models\Supplier;
use addons\Warehouse\common\models\Warehouse;
use common\models\backend\Member;

/**
 * This is the model class for table "purchase_receipt".
 *
 * @property int $id ID
 * @property int $merchant_id
 * @property int $supplier_id 供应商
 * @property string $receipt_no 收货单号
 * @property string $purchase_sn 采购单号
 * @property int $purchase_type 采购类型
 * @property int $receipt_status 单据状态
 * @property int $receipt_num 出货数量
 * @property string $delivery_no 工厂出货单号
 * @property int $total_stone_num 总粒数
 * @property string $total_weight 总重量
 * @property string $total_cost 总金额（总成本）
 * @property int $put_in_type 入库方式
 * @property int $to_warehouse_id 入库仓库
 * @property int $is_to_warehouse 是否入库
 * @property int $stock_status 入库状态
 * @property int $stock_num 入库数量
 * @property int $auditor_id 审核人
 * @property int $audit_status 审核状态
 * @property int $audit_time 审核时间
 * @property string $audit_remark 审核备注
 * @property string $remark 备注
 * @property int $sort 排序
 * @property int $status 状态 1启用 0禁用 -1删除
 * @property int $creator_id 创建人
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class PurchaseReceipt extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('purchase_receipt');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'merchant_id', 'supplier_id', 'purchase_type', 'receipt_status', 'receipt_num', 'total_stone_num', 'put_in_type', 'to_warehouse_id', 'is_to_warehouse', 'stock_status', 'stock_num', 'auditor_id', 'audit_status', 'audit_time', 'sort', 'status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['supplier_id'], 'required'],
            [['total_weight', 'total_cost'], 'number'],
            [['receipt_no', 'purchase_sn', 'delivery_no'], 'string', 'max' => 30],
            [['audit_remark', 'remark'], 'string', 'max' => 255],
            ['receipt_no', 'unique'],
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
            'supplier_id' => '供应商',
            'receipt_no' => '收货单号',
            'purchase_sn' => '采购单号',
            'purchase_type' => '采购类型',
            'receipt_status' => '单据状态',
            'receipt_num' => '出货数量',
            'delivery_no' => '工厂出货单号',
            'total_stone_num' => '总粒数',
            'total_weight' => '总重量',
            'total_cost' => '总成本',
            'put_in_type' => '入库方式',
            'to_warehouse_id' => '入库仓库',
            'is_to_warehouse' => '是否入库',
            'stock_status' => '入库状态',
            'stock_num' => '已入库数量',
            'auditor_id' => '审核人',
            'audit_status' => '审核状态',
            'audit_time' => '审核时间',
            'audit_remark' => '审核备注',
            'remark' => '备注',
            'sort' => '排序',
            'status' => '状态',
            'creator_id' => '创建人',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
    /**
     * 关联采购收货单明细表
     * @return \yii\db\ActiveQuery
     */
    public function getReceiptGoods(){
        return $this->hasMany(PurchaseReceiptGoods::class, ['id'=>'receipt_id']);
    }
    /**
     * 供应商 一对一
     * @return \yii\db\ActiveQuery
     */
    public function getSupplier()
    {
        return $this->hasOne(Supplier::class, ['id'=>'supplier_id'])->alias('supplier');
    }
    /**
     * 入库仓库 一对一
     * @return \yii\db\ActiveQuery
     */
    public function getToWarehouse()
    {
        return $this->hasOne(Warehouse::class, ['id'=>'to_warehouse_id'])->alias('toWarehouse');
    }
    /**
     * 创建人
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(Member::class, ['id'=>'creator_id'])->alias('creator');
    }
    /**
     * 审核人
     * @return \yii\db\ActiveQuery
     */
    public function getAuditor()
    {
        return $this->hasOne(Member::class, ['id'=>'auditor_id'])->alias('auditor');
    }
}
