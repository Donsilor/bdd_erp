<?php

namespace addons\Sales\common\models;

use Yii;
use common\models\common\PayLog;

/**
 * This is the model class for table "sales_order".
 *
 * @property int $id ID
 * @property int $merchant_id 商户
 * @property string $language 订单语言
 * @property string $currency 订单货币
 * @property string $order_sn 订单编号
 * @property string $pay_sn 支付单号
 * @property int $pay_type 支付方式 0待支付 1微信 2支付宝 3银联 6Paypal 100线下
 * @property int $pay_status 支付状态 1已支付 0 未支付
 * @property int $pay_time 支付(付款)时间
 * @property int $finished_time 订单完成时间
 * @property int $order_status 订单状态：0(已取消)10(默认):未付款;20:已付款;30:已发货;40:已完成;
 * @property int $refund_status 退款状态(0无退款,1部分退款,2全部退款)
 * @property int $express_id 快递方式
 * @property string $express_no 物流单号
 * @property int $distribute_status 配货状态(0未配货 1允许配货 2配货中 3已配货)
 * @property int $delivery_status 发货状态(0未发货,1已发货)
 * @property int $delivery_time 发货时间
 * @property int $receive_type 收货类型(1随时 2工作日 3周日)
 * @property int $order_from 订单来源
 * @property int $order_type 订单类型 1现货 2定制 3赠品
 * @property int $is_invoice 是否开发票
 * @property string $out_trade_no 外部订单号
 * @property int $follower_id 跟进人
 * @property int $followed_time 跟进时间
 * @property int $followed_status 跟进状态 1已跟进 0未跟进
 * @property int $area_id 订单区域
 * @property int $audit_status 审核状态
 * @property int $audit_time 审核时间
 * @property int $customer_id 客户ID
 * @property string $customer_name 客户姓名
 * @property string $customer_mobile 客户手机
 * @property string $customer_email 客户邮箱
 * @property string $customer_message 客户留言
 * @property string $store_remark 商家备注
 * @property string $remark 订单备注
 * @property int $creator_id 创建人
 * @property int $created_at 订单生成时间
 * @property int $updated_at 更新时间
 */
class Order extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('order');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sale_channel_id','language','currency','customer_mobile'], 'required'],
            [['merchant_id', 'goods_num','sale_channel_id','customer_from','pay_type', 'pay_status', 'pay_time', 'finished_time', 'order_status', 'refund_status', 'express_id', 'distribute_status', 'delivery_status', 'delivery_time', 'receive_type', 'order_from', 'order_type', 'is_invoice', 'follower_id', 'followed_time', 'followed_status', 'area_id', 'audit_status', 'audit_time', 'customer_id', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['order_sn'], 'required'],
            [['language'], 'string', 'max' => 5],
            [['currency'], 'string', 'max' => 3],
            [['order_sn'], 'string', 'max' => 20],
            [['pay_sn'], 'string', 'max' => 32],
            [['express_no', 'out_trade_no'], 'string', 'max' => 50],
            [['customer_name'], 'string', 'max' => 60],
            [['customer_mobile'], 'string', 'max' => 30],
            [['customer_email'], 'string', 'max' => 120],
            [['customer_message', 'store_remark'], 'string', 'max' => 500],
            [['remark'], 'string', 'max' => 255],
            [['order_sn'], 'unique'],
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
            'language' => '订单语言',
            'currency' => '订单货币',
            'order_sn' => '订单编号',
            'sale_channel_id' => '销售渠道',
            'pay_sn' => '支付单号',
            'pay_type' => '支付方式',
            'pay_status' => '支付状态',
            'pay_time' => '支付时间',
            'finished_time' => '订单完成时间',
            'order_status' => '订单状态',
            'refund_status' => '退款状态',
            'express_id' => '快递方式',
            'express_no' => '物流单号',
            'distribute_status' => '配货状态',
            'delivery_status' => '发货状态',
            'delivery_time' => '发货时间',
            'receive_type' => '收货类型',
            'order_from' => '订单来源',
            'order_type' => '订单类型',
            'goods_num' => '商品数量',
            'is_invoice' => '是否开发票',
            'out_trade_no' => '外部订单号',
            'follower_id' => '跟单人',
            'followed_time' => '跟进时间',
            'followed_status' => '跟进状态',
            'area_id' => '订单区域',
            'audit_status' => '审核状态',
            'audit_time' => '审核时间',
            'customer_from' => '客户来源',
            'customer_id' => '客户ID',
            'customer_name' => '客户姓名',
            'customer_mobile' => '客户手机',
            'customer_email' => '客户邮箱',
            'customer_message' => '客户留言',
            'store_remark' => '商家备注',
            'remark' => '订单备注',
            'creator_id' => '创建人',
            'created_at' => '订单时间',
            'updated_at' => '更新时间',
        ];
    }
    
    /**
     * 对应订单付款信息模型
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(OrderAccount::class, ['order_id'=>'id']);
    }
    
    /**
     * 对应订单付款信息模型
     * @return \yii\db\ActiveQuery
     */
    public function getInvoice()
    {
        return $this->hasOne(OrderInvoice::class, ['order_id'=>'id']);
    }
    
    /**
     * 对应订单地址模型
     * @return \yii\db\ActiveQuery
     */
    public function getAddress()
    {
        return $this->hasOne(OrderAddress::class, ['order_id'=>'id']);
    }
    
    /**
     * 对应买家模型
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['id'=>'customer_id']);
    }
    
    /**
     * 对应跟进人（管理员）模型
     * @return \yii\db\ActiveQuery
     */
    public function getFollower()
    {
        return $this->hasOne(\common\models\backend\Member::class, ['id'=>'follower_id']);
    }
    
    /**
     * 对应订单商品信息模型
     * @return \yii\db\ActiveQuery
     */
    public function getGoods()
    {
        return $this->hasMany(OrderGoods::class,['order_id'=>'id']);
    }
    /**
     * 对应快递模型
     * @return \yii\db\ActiveQuery
     */
    public function getExpress()
    {
        return $this->hasOne(Express::class, ['id'=>'express_id']);
    }
    /**
     * 对应快递模型
     * @return \yii\db\ActiveQuery
     */
    public function getSaleChannel()
    {
        return $this->hasOne(SaleChannel::class, ['id'=>'sale_channel_id']);
    }
    /**
     * 对应订单商品信息模型
     * @return \yii\db\ActiveQuery
     */
    public function getPaylogs()
    {
        return $this->hasMany(PayLog::class,['order_sn'=>'order_sn']);
    }
}
