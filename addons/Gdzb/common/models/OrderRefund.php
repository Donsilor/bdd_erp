<?php

namespace addons\Gdzb\common\models;

use addons\Sales\common\models\SaleChannel;
use addons\Warehouse\common\models\Warehouse;
use common\models\backend\Member;
use Yii;

/**
 * This is the model class for table "gdzb_order_refund".
 *
 * @property int $id ID
 * @property int $merchant_id 商户
 * @property int $order_id 订单Id
 * @property string $refund_sn 退货单号
 * @property string $refund_amount 退货金额
 * @property int $refund_num 退货数量
 * @property int $channel_id 所属渠道
 * @property int $warehouse_id 所属仓库
 * @property int $refund_status 退款状态(0无退款,1部分退款,2全部退款)
 * @property int $audit_status 审核状态
 * @property int $audit_time 审核时间
 * @property int $auditor_id 审核人
 * @property string $audit_remark 审核备注
 * @property int $customer_id 客户ID
 * @property string $remark 订单备注
 * @property int $creator_id 创建人
 * @property int $created_at 订单生成时间
 * @property int $updated_at 更新时间
 */
class OrderRefund extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('order_refund');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'order_id', 'refund_num', 'channel_id', 'warehouse_id', 'refund_status', 'audit_status', 'audit_time', 'auditor_id', 'customer_id', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['refund_amount'], 'number'],
            [['refund_sn'], 'string', 'max' => 20],
            [['audit_remark', 'remark'], 'string', 'max' => 255],
            [['refund_sn'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'merchant_id' => '商户',
            'order_id' => '订单Id',
            'refund_sn' => '退货单号',
            'refund_amount' => '退货金额',
            'refund_num' => '退货数量',
            'channel_id' => '所属渠道',
            'warehouse_id' => '所属仓库',
            'refund_status' => '退款状态',
            'audit_status' => '审核状态',
            'audit_time' => '审核时间',
            'auditor_id' => '审核人',
            'audit_remark' => '审核备注',
            'customer_id' => '客户ID',
            'remark' => '订单备注',
            'creator_id' => '创建人',
            'created_at' => '创建时间',
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
     * 对应买家模型
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['id'=>'customer_id'])->alias('customer');
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
     * 对应渠道
     * @return \yii\db\ActiveQuery
     */
    public function getSaleChannel()
    {
        return $this->hasOne(SaleChannel::class, ['id'=>'channel_id'])->alias('saleChannel');
    }

    /**
     * 对应订单模型
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::class,['id'=>'order_id'])->alias('order');
    }


    /**
     * 对应仓库
     * @return \yii\db\ActiveQuery
     */
    public function getWarehouse()
    {
        return $this->hasOne(Warehouse::class, ['id'=>'warehouse_id'])->alias('warehouse');
    }

}
