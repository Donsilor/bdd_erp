<?php

namespace addons\Sales\common\models;

use Yii;

/**
 * This is the model class for table "sales_return".
 *
 * @property int $id ID
 * @property string $return_no 退款编号
 * @property int $order_id 订单ID
 * @property string $order_sn 订单号
 * @property int $order_detail_id 订单明细ID
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
 * @property int $check_status 审核状态(0.未操作，1.主管审核通过，2.库管审核通过，3.财务审核通过)
 * @property string $remark 备注
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
        return self::tableFullName('sales_return');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['return_no', 'order_id', 'order_sn', 'order_detail_id'], 'required'],
            [['order_id', 'order_detail_id', 'channel_id', 'goods_num', 'return_by', 'return_type', 'customer_id', 'is_finance_refund', 'is_quick_refund', 'check_status', 'status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['should_amount', 'apply_amount', 'real_amount'], 'number'],
            [['return_no', 'customer_mobile'], 'string', 'max' => 30],
            [['order_sn'], 'string', 'max' => 50],
            [['return_reason', 'remark'], 'string', 'max' => 255],
            [['customer_name'], 'string', 'max' => 60],
            [['customer_email'], 'string', 'max' => 120],
            [['currency'], 'string', 'max' => 3],
            [['bank_name', 'bank_card'], 'string', 'max' => 100],
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
            'order_detail_id' => '订单明细ID',
            'channel_id' => '所属渠道',
            'goods_num' => '商品数量',
            'should_amount' => '应退金额',
            'apply_amount' => '申请退款金额',
            'real_amount' => '实退金额',
            'return_reason' => '退款原因',
            'return_by' => '退款方式(1.退商品，2.不退商品)',
            'return_type' => '退款类型(1.打卡，2.转单)',
            'customer_id' => '客户ID',
            'customer_name' => '客户姓名',
            'customer_mobile' => '客户手机',
            'customer_email' => '客户邮箱',
            'currency' => '货币',
            'bank_name' => '开户银行',
            'bank_card' => '银行账户',
            'is_finance_refund' => '是否财务退款',
            'is_quick_refund' => '是否快速退款',
            'check_status' => '审核状态(0.未操作，1.主管审核通过，2.库管审核通过，3.财务审核通过)',
            'remark' => '备注',
            'status' => '状态 1启用 0禁用 -1删除',
            'creator_id' => '创建人',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
