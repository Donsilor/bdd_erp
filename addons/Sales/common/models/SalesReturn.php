<?php

namespace addons\Sales\common\models;

use Yii;
use common\models\backend\Member;

/**
 * This is the model class for table "sales_return".
 *
 * @property int $id ID
 * @property string $return_no 退款编号
 * @property int $order_id 订单ID
 * @property string $order_sn 订单号
 * @property int $channel_id 所属渠道
 * @property int $goods_num 商品数量
 * @property string $should_amount 应退金额
 * @property string $apply_amount 申请退款金额
 * @property string $real_amount 实退金额
 * @property string $return_reason 退款原因
 * @property int $return_by 退款方式(1.退商品，2.不退商品)
 * @property int $return_type 退款类型(1.打卡，2.转单)
 * @property int $customer_id 客户ID
 * @property string $customer_name 客户姓名
 * @property string $customer_mobile 客户手机
 * @property string $customer_email 客户邮箱
 * @property string $currency 货币
 * @property string $bank_name 开户银行
 * @property string $bank_card 银行账户
 * @property int $is_finance_refund 是否财务退款
 * @property int $is_quick_refund 是否快速退款
 * @property int $leader_id 部门主管
 * @property int $leader_status 主管审核状态
 * @property string $leader_remark 主管审核意见
 * @property int $leader_time 主管审核时间
 * @property int $storekeeper_id 库管
 * @property int $storekeeper_status 库管审核
 * @property string $storekeeper_remark 库管审核备注
 * @property int $storekeeper_time 库管审核时间
 * @property int $finance_id 财务
 * @property int $finance_status 财务审核状态
 * @property string $finance_remark 财务审核备注
 * @property int $finance_time 财务审核时间
 * @property int $payer_id 实际付款人
 * @property int $pay_status 支付状态
 * @property string $pay_remark 付款备注
 * @property string $pay_receipt 付款凭证
 * @property int $check_status 确认状态(0.未操作，1.主管确认通过，2.库管确认通过，3.财务确认通过)
 * @property string $bill_no 仓储单据编号
 * @property int $new_order_id 新订单ID
 * @property string $new_order_sn 新订单号
 * @property string $remark 备注
 * @property int $return_status 单据状态
 * @property int $audit_status 审核状态
 * @property string $audit_remark 审核备注
 * @property int $audit_time 审核时间
 * @property int $auditor_id 审核人
 * @property int $status 状态 1启用 0禁用 -1删除
 * @property int $creator_id 创建人
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class SalesReturn extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('return');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['return_no', 'order_id', 'order_sn'], 'required'],
            [['order_id', 'channel_id', 'goods_num', 'return_by', 'return_type', 'customer_id', 'is_finance_refund', 'is_quick_refund', 'leader_id', 'leader_status', 'leader_time', 'storekeeper_id', 'storekeeper_status', 'storekeeper_time', 'finance_id', 'finance_status', 'finance_time', 'payer_id', 'pay_status', 'check_status', 'return_status', 'audit_status', 'audit_time', 'auditor_id', 'status', 'new_order_id', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['should_amount', 'apply_amount', 'real_amount'], 'number'],
            [['return_no', 'customer_mobile'], 'string', 'max' => 30],
            [['order_sn', 'bill_no', 'new_order_sn'], 'string', 'max' => 50],
            [['return_reason'], 'string', 'max' => 10],
            [['customer_name'], 'string', 'max' => 60],
            [['customer_email'], 'string', 'max' => 120],
            [['currency'], 'string', 'max' => 3],
            [['bank_name', 'bank_card'], 'string', 'max' => 100],
            [['leader_remark', 'storekeeper_remark', 'finance_remark', 'pay_remark', 'pay_receipt', 'remark', 'audit_remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'return_no' => '退款编号',
            'order_id' => '订单ID',
            'order_sn' => '订单号',
            'channel_id' => '所属渠道',
            'goods_num' => '退款数量',
            'should_amount' => '应退金额',
            'apply_amount' => '申请退款金额',
            'real_amount' => '实退金额',
            'return_reason' => '退款原因',
            'return_by' => '退款方式',
            'return_type' => '退款类型',
            'customer_id' => '客户ID',
            'customer_name' => '客户姓名',
            'customer_mobile' => '客户手机',
            'customer_email' => '客户邮箱',
            'currency' => '货币',
            'bank_name' => '开户银行',
            'bank_card' => '银行账户',
            'is_finance_refund' => '是否财务线下退款',
            'is_quick_refund' => '是否平台极速退款',
            'leader_id' => '部门主管',
            'leader_status' => '主管确认',
            'leader_remark' => '主管确认意见',
            'leader_time' => '主管确认时间',
            'storekeeper_id' => '库管',
            'storekeeper_status' => '库管确认',
            'storekeeper_remark' => '库管确认备注',
            'storekeeper_time' => '库管确认时间',
            'finance_id' => '财务',
            'finance_status' => '财务确认',
            'finance_remark' => '财务确认备注',
            'finance_time' => '财务确认时间',
            'payer_id' => '实际付款人',
            'pay_status' => '支付状态',
            'pay_remark' => '付款备注',
            'pay_receipt' => '付款凭证',
            'check_status' => '确认状态',
            'remark' => '退款备注',
            'bill_no' => '仓储单据编号',
            'new_order_id' => '新订单ID',
            'new_order_sn' => '新订单号(转单)',
            'return_status' => '退款单状态',
            'audit_status' => '审核状态',
            'audit_remark' => '审核备注',
            'audit_time' => '审核时间',
            'auditor_id' => '审核人',
            'status' => '状态',
            'creator_id' => '申请人',
            'created_at' => '申请时间',
            'updated_at' => '更新时间',
        ];
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
     * 付款人
     * @return \yii\db\ActiveQuery
     */
    public function getPayer()
    {
        return $this->hasOne(Member::class, ['id'=>'creator_id'])->alias('payer');
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
     * 部门主管
     * @return \yii\db\ActiveQuery
     */
    public function getLeader()
    {
        return $this->hasOne(Member::class, ['id'=>'leader_id'])->alias('leader');
    }
    /**
     * 库管
     * @return \yii\db\ActiveQuery
     */
    public function getStorekeeper()
    {
        return $this->hasOne(Member::class, ['id'=>'storekeeper_id'])->alias('storekeeper');
    }
    /**
     * 财务
     * @return \yii\db\ActiveQuery
     */
    public function getFinance()
    {
        return $this->hasOne(Member::class, ['id'=>'finance_id'])->alias('finance');
    }
    /**
     * 渠道
     * @return \yii\db\ActiveQuery
     */
    public function getChannel()
    {
        return $this->hasOne(SaleChannel::class, ['id'=>'channel_id'])->alias('channel');
    }

    /**
     * 关联退款原因
     * @return \yii\db\ActiveQuery
     */
    public function getConfig()
    {
        return $this->hasOne(ReturnConfig::class, ['id'=>'return_reason'])->alias('config');
    }
}
