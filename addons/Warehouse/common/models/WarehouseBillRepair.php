<?php

namespace addons\Warehouse\common\models;

use addons\Supply\common\models\Supplier;
use addons\Warehouse\common\models\BaseModel;
use common\models\backend\Member;
use Yii;

/**
 * This is the model class for table "warehouse_bill_repair".
 *
 * @property int $id ID
 * @property int $repair_no 维修单号
 * @property int $order_id 订单ID
 * @property string $order_sn 订单号
 * @property string $produce_sn 布产号
 * @property int $goods_id 货号
 * @property string $consignee 客户姓名
 * @property int $repair_type 维修单类型
 * @property string $repair_act 维修动作
 * @property int $supplier_id 工厂
 * @property int $repair_times 维修次数
 * @property int $repair_status 状态
 * @property string $bill_m_no 转仓单号
 * @property int $follower_id 跟单人
 * @property int $qc_status 质检状态：0，未质检；1，质检通过；2，质检未过；
 * @property string $repair_price 维修费用
 * @property int $qc_times 质检次数
 * @property int $orders_time 下单时间
 * @property int $predict_time 预计出厂时间
 * @property int $end_time 完成时间
 * @property int $receiving_time 收货时间
 * @property int $qc_nopass_time 最新质检未通过时间
 * @property string $remark 备注
 * @property int $auditor_id 审核人
 * @property int $audit_status 审核状态
 * @property int $audit_time 审核时间
 * @property string $audit_remark 审核备注
 * @property int $status 状态 1启用 0禁用 -1 删除
 * @property int $creator_id 创建人
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class WarehouseBillRepair extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_bill_repair');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'repair_type', 'supplier_id', 'repair_times', 'repair_status', 'follower_id', 'qc_status', 'qc_times', 'orders_time', 'predict_time', 'end_time', 'receiving_time', 'qc_nopass_time', 'auditor_id', 'audit_status', 'audit_time', 'status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['repair_price'], 'number'],
            [['repair_no', 'order_sn', 'produce_sn', 'consignee', 'bill_m_no'], 'string', 'max' => 30],
            //[['repair_act'], 'string', 'max' => 100],
            [['repair_act'], 'RepairActScope'],
            [['remark', 'audit_remark'], 'string', 'max' => 255],
            [['goods_id'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'repair_no' => '维修单号',
            'order_id' => '订单ID',
            'order_sn' => '订单号',
            'produce_sn' => '布产号',
            'goods_id' => '货号',
            'consignee' => '客户姓名',
            'repair_type' => '维修类型',
            'repair_act' => '维修动作',
            'supplier_id' => '维修工厂',
            'repair_times' => '维修次数',
            'repair_status' => '维修状态',
            'bill_m_no' => '调拨单号',
            'follower_id' => '跟单人',
            'qc_status' => '质检状态',
            'repair_price' => '维修费用',
            'qc_times' => '质检次数',
            'orders_time' => '下单时间',
            'predict_time' => '预计出厂时间',
            'end_time' => '完成时间',
            'receiving_time' => '收货时间',
            'qc_nopass_time' => '最新质检未通过时间',
            'remark' => '备注',
            'auditor_id' => '审核人',
            'audit_status' => '审核状态',
            'audit_time' => '审核时间',
            'audit_remark' => '审核备注',
            'status' => '状态',
            'creator_id' => '创建人',
            'created_at' => '制单时间',
            'updated_at' => '更新时间',
        ];
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
    /**
     * 跟单人
     * @return \yii\db\ActiveQuery
     */
    public function getFollower()
    {
        return $this->hasOne(Member::class, ['id'=>'follower_id'])->alias('follower');
    }
    /**
     * 维修动作
     */
    public function RepairActScope()
    {
        if(is_array($this->repair_act)){
            $this->repair_act = implode(',',$this->repair_act);
        }
        return $this->repair_act;
    }
}
