<?php

namespace addons\Gdzb\common\models;

use Yii;

/**
 * This is the model class for table "gdzb_order".
 *
 * @property int $id ID
 * @property int $merchant_id 商户
 * @property string $language 订单语言
 * @property string $currency 货币
 * @property string $order_sn 订单编号
 * @property int $channel_id 销售渠道
 * @property int $goods_num 商品数量
 * @property string $pay_sn 支付单号
 * @property int $pay_type 支付方式 0待支付 1微信 2支付宝 3银联 6Paypal 100线下
 * @property int $pay_status 支付状态 1已支付 0 未支付
 * @property int $pay_time 支付(付款)时间
 * @property int $finished_time 订单完成时间
 * @property int $order_time 下单时间
 * @property int $order_status 订单状态：0保存 1待审核 2已审核 3已关闭 4已取消
 * @property int $refund_status 退款状态(0无退款,1部分退款,2全部退款)
 * @property int $express_id 快递方式
 * @property string $express_no 物流单号
 * @property int $delivery_status 发货状态(0未发货,1已发货)
 * @property int $delivery_time 发货时间
 * @property int $follower_id 跟进人
 * @property int $followed_time 跟进时间
 * @property int $followed_status 跟进状态 1已跟进 0未跟进
 * @property int $audit_status 审核状态
 * @property int $audit_time 审核时间
 * @property int $auditor_id 审核人
 * @property string $audit_remark 审核备注
 * @property int $customer_id 客户ID
 * @property string $customer_name 客户姓名
 * @property string $customer_mobile 客户手机
 * @property string $customer_weixin 客户微信
 * @property string $customer_message 客户留言
 * @property string $customer_account 客户付款账号
 * @property string $consignee_info 收货人信息(json)
 * @property string $pay_remark 付款备注
 * @property string $remark 订单备注
 * @property int $is_invoice 是否开发票
 * @property string $invoice_info 发票信息(json)
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
            [['merchant_id', 'channel_id', 'goods_num', 'pay_type', 'pay_status', 'pay_time', 'finished_time', 'order_time', 'order_status', 'refund_status', 'express_id', 'delivery_status', 'delivery_time', 'follower_id', 'followed_time', 'followed_status', 'audit_status', 'audit_time', 'auditor_id', 'customer_id', 'is_invoice', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['language'], 'string', 'max' => 5],
            [['currency'], 'string', 'max' => 3],
            [['order_sn'], 'string', 'max' => 20],
            [['pay_sn'], 'string', 'max' => 32],
            [['express_no'], 'string', 'max' => 50],
            [['audit_remark', 'pay_remark', 'remark'], 'string', 'max' => 255],
            [['customer_name'], 'string', 'max' => 60],
            [['customer_mobile'], 'string', 'max' => 30],
            [['customer_weixin', 'customer_account'], 'string', 'max' => 120],
            [['customer_message'], 'string', 'max' => 500],
            [['consignee_info', 'invoice_info'], 'string', 'max' => 1000],
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
            'currency' => '货币',
            'order_sn' => '订单编号',
            'channel_id' => '销售渠道',
            'goods_num' => '商品数量',
            'pay_sn' => '支付单号',
            'pay_type' => '支付方式 0待支付 1微信 2支付宝 3银联 6Paypal 100线下',
            'pay_status' => '支付状态 1已支付 0 未支付',
            'pay_time' => '支付(付款)时间',
            'finished_time' => '订单完成时间',
            'order_time' => '下单时间',
            'order_status' => '订单状态：0保存 1待审核 2已审核 3已关闭 4已取消',
            'refund_status' => '退款状态(0无退款,1部分退款,2全部退款)',
            'express_id' => '快递方式',
            'express_no' => '物流单号',
            'delivery_status' => '发货状态(0未发货,1已发货)',
            'delivery_time' => '发货时间',
            'follower_id' => '跟进人',
            'followed_time' => '跟进时间',
            'followed_status' => '跟进状态 1已跟进 0未跟进',
            'audit_status' => '审核状态',
            'audit_time' => '审核时间',
            'auditor_id' => '审核人',
            'audit_remark' => '审核备注',
            'customer_id' => '客户ID',
            'customer_name' => '客户姓名',
            'customer_mobile' => '客户手机',
            'customer_weixin' => '客户微信',
            'customer_message' => '客户留言',
            'customer_account' => '客户付款账号',
            'consignee_info' => '收货人信息(json)',
            'pay_remark' => '付款备注',
            'remark' => '订单备注',
            'is_invoice' => '是否开发票',
            'invoice_info' => '发票信息(json)',
            'creator_id' => '创建人',
            'created_at' => '订单生成时间',
            'updated_at' => '更新时间',
        ];
    }
}
