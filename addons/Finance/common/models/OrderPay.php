<?php

namespace addons\Finance\common\models;

use addons\Sales\common\models\Order;
use common\models\backend\Member;

use Yii;

/**
 * This is the model class for table "finance_order_pay".
 *
 * @property int $id ID
 * @property int $order_id 订单ID
 * @property string $pay_sn 支付单号
 * @property string $pay_amount 点款金额
 * @property int $pay_type 支付方式
 * @property int $pay_status 支付状态 1已支付 0未支付
 * @property string $currency 货币
 * @property double $exchange_rate 汇率
 * @property string $creator 点款人
 * @property int $creator_id
 * @property int $created_at 点款时间
 * @property int $updated_at
 */
class OrderPay extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('order_pay');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'pay_type', 'pay_status', 'creator_id', 'created_at', 'updated_at','arrive_type','arrive_status'], 'integer'],
            [['pay_amount', 'exchange_rate'], 'number'],
            [['pay_sn', 'creator'], 'string', 'max' => 30],
            [['remark'], 'string', 'max' => 255],
            [['currency'], 'string', 'max' => 3],
            ['arrival_time','safe']
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
            'pay_sn' => '支付单号',
            'pay_amount' => '点款金额',
            'pay_type' => '支付方式',
            'pay_status' => '支付状态',
            'currency' => '货币',
            'exchange_rate' => '汇率',
            'creator' => '点款人',
            'arrive_type' => '到账方式',
            'arrive_status' => '到账状态',
            'arrival_time' => '到账时间',
            'remark' => '备注',
            'creator_id' => '点款人ID',
            'created_at' => '点款时间',
            'updated_at' => '更新时间',
        ];
    }


    /**
     * 订单
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id'=>'order_id'])->alias('order');
    }
    /**
     * 创建人
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(Member::class, ['id'=>'creator_id'])->alias('creator');
    }
}
