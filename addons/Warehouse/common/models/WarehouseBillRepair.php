<?php

namespace addons\Warehouse\common\models;

use addons\Warehouse\common\models\BaseModel;
use Yii;

/**
 * This is the model class for table "warehouse_bill_repair".
 *
 * @property int $id ID
 * @property int $order_id 订单ID
 * @property string $order_sn 订单号
 * @property string $produce_sn 布产号
 * @property int $goods_id 货号
 * @property string $consignee 客户姓名
 * @property int $repair_type 维修单类型
 * @property string $repair_act 维修动作
 * @property int $repair_factory 工厂
 * @property int $weixiu_times 维修次数
 * @property int $sale_weixiu 售后维修
 * @property int $repair_status 状态
 * @property string $bill_m_no 转仓单号
 * @property int $follower_id 跟单人
 * @property int $qc_status 质检状态：1，质检通过；2，质检未过；3，未质检；
 * @property string $weixiu_price 维修费用
 * @property int $qc_times 质检次数
 * @property int $orders_at 下单时间
 * @property int $predict_at 预计出厂时间
 * @property int $end_at 完成时间
 * @property int $receiving_at 收货时间
 * @property int $qc_nopass_at 最新质检未通过时间
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
            [['id', 'order_id', 'goods_id', 'repair_type', 'repair_factory', 'weixiu_times', 'sale_weixiu', 'repair_status', 'follower_id', 'qc_status', 'qc_times', 'orders_at', 'predict_at', 'end_at', 'receiving_at', 'qc_nopass_at', 'auditor_id', 'audit_status', 'audit_time', 'status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['weixiu_price'], 'number'],
            [['order_sn', 'produce_sn', 'consignee', 'bill_m_no'], 'string', 'max' => 30],
            [['repair_act'], 'string', 'max' => 100],
            [['remark', 'audit_remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => '订单ID',
            'order_sn' => '订单号',
            'produce_sn' => '布产号',
            'goods_id' => '货号',
            'consignee' => '客户姓名',
            'repair_type' => '维修单类型',
            'repair_act' => '维修动作',
            'repair_factory' => '工厂',
            'weixiu_times' => '维修次数',
            'sale_weixiu' => '售后维修',
            'repair_status' => '状态',
            'bill_m_no' => '转仓单号',
            'follower_id' => '跟单人',
            'qc_status' => '质检状态：1，质检通过；2，质检未过；3，未质检；',
            'weixiu_price' => '维修费用',
            'qc_times' => '质检次数',
            'orders_at' => '下单时间',
            'predict_at' => '预计出厂时间',
            'end_at' => '完成时间',
            'receiving_at' => '收货时间',
            'qc_nopass_at' => '最新质检未通过时间',
            'remark' => '备注',
            'auditor_id' => '审核人',
            'audit_status' => '审核状态',
            'audit_time' => '审核时间',
            'audit_remark' => '审核备注',
            'status' => '状态 1启用 0禁用 -1 删除',
            'creator_id' => '创建人',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
