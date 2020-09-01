<?php

namespace addons\Sales\common\models;

use common\models\backend\Member;
use Yii;
use common\models\common\PayLog;
use common\helpers\RegularHelper;
use addons\Finance\common\models\OrderPay;

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
 * @property int $out_pay_time 外部支付时间
 * @property int $finished_time 订单完成时间
 * @property int $order_time 下单时间
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
 * @property string $out_pay_no 外部支付交易号
 * @property int $sale_channel_id 销售渠道
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
 * @property string $customer_account 客户付款账户
 * @property string $store_account 公司收款账户
 * @property string $pay_remark 付款备注
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
            [['sale_channel_id','language','currency','customer_name'], 'required'],
            [['merchant_id', 'goods_num','apply_id','sale_channel_id','pay_type', 'pay_status', 'pay_time','out_pay_time','order_time', 'finished_time', 'order_status', 'refund_status', 'express_id', 'distribute_status', 'delivery_status', 'delivery_time', 'receive_type', 'order_from', 'order_type', 'is_invoice', 'follower_id', 'followed_time', 'followed_status', 'area_id', 'audit_status', 'audit_time', 'auditor_id','customer_id', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['language'], 'string', 'max' => 5],
            [['currency'], 'string', 'max' => 3],
            [['order_sn'], 'string', 'max' => 20],
            [['pay_sn'], 'string', 'max' => 32],
            [['express_no', 'out_trade_no','out_pay_no'], 'string', 'max' => 50],
            [['customer_name'], 'string', 'max' => 60],
            [['customer_mobile'], 'string', 'max' => 30],
            [['customer_email','customer_account','store_account'], 'string', 'max' => 120],
            ['customer_email', 'match', 'pattern' => RegularHelper::email(), 'message' => '邮箱地址不合法'],
            [['customer_message', 'store_remark'], 'string', 'max' => 500],
            [['remark','audit_remark','pay_remark'], 'string', 'max' => 255],
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
            'pay_sn' => '点款支付单号',
            'apply_id' => '申请单ID',
            'pay_type' => '支付方式',
            'pay_status' => '支付状态',
            'pay_time' => '支付时间',
            'goods_num' => '商品数量',
            'sale_channel_id' => '销售渠道',
            'finished_time' => '订单完成时间',
            'order_status' => '订单状态',
            'refund_status' => '退款状态',
            'express_id' => '快递公司',
            'express_no' => '快递单号',
            'distribute_status' => '配货状态',
            'delivery_status' => '发货状态',
            'delivery_time' => '发货时间',
            'receive_type' => '收货类型',
            'order_from' => '录单来源',
            'order_type' => '订单类型',
            'is_invoice' => '是否开发票',
            'out_trade_no' => '外部订单号',
            'out_pay_no' => '外部支付交易号',
            'out_pay_time' => '外部支付时间',
            'follower_id' => '跟单人',
            'followed_time' => '跟进时间',
            'followed_status' => '跟进状态',
            'area_id' => '订单区域',
            'audit_status' => '审核状态',
            'audit_time' => '审核时间',
            'auditor_id' => '审核人',
            'audit_remark' => '审核备注',            
            'customer_id' => '客户ID',
            'customer_name' => '客户姓名',
            'customer_mobile' => '客户手机',
            'customer_email' => '客户邮箱',
            'customer_message' => '客户留言',
            'customer_account' => '客户付款账户',
            'store_account' => '公司收款账户',
            'pay_remark' => '付款备注',
            'store_remark' => '商家备注',
            'remark' => '订单备注',
            'creator_id' => '创建人',
            'order_time' => '下单时间',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            if(isset(Yii::$app->user)) {
                $this->creator_id = Yii::$app->user->identity->getId();
                if(!$this->order_time) {
                    $this->order_time = time();
                }
            }else{
                $this->creator_id = 0;
            }
        }
        return parent::beforeSave($insert);
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
     * 支付方式 一对一
     * @return \yii\db\ActiveQuery
     */
    public function getPayType()
    {
        return $this->hasOne(Payment::class, ['id'=>'pay_type']);
    }
    /**
     * 支付记录 一对一
     * @return \yii\db\ActiveQuery
     */
    public function getPayLogs()
    {
        return $this->hasMany(OrderPay::class, ['order_id'=>'id'])->alias('payLogs');
    }
    /**
     * 对应快递模型
     * @return \yii\db\ActiveQuery
     */
    public function getSaleChannel()
    {
        return $this->hasOne(SaleChannel::class, ['id'=>'sale_channel_id'])->alias('saleChannel');
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
