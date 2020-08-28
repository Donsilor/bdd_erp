<?php

namespace addons\Sales\common\models;

use Yii;

/**
 * This is the model class for table "sales_order_account".
 *
 * @property int $order_id 订单ID
 * @property int $merchant_id 商户ID
 * @property string $order_amount 订单总金额
 * @property string $goods_amount 商品总金额
 * @property string $discount_amount 总优惠金额
 * @property string $goods_discount
 * @property string $order_discount
 * @property string $pay_amount 订单应付金额
 * @property string $refund_amount 退款金额
 * @property string $shipping_fee 运费
 * @property string $tax_fee 税费
 * @property string $safe_fee 保险费
 * @property string $other_fee 附加费
 * @property double $exchange_rate 汇率
 * @property string $currency 货币代号
 * @property string $coupon_amount 优惠券优惠金额
 * @property string $card_amount 优惠券优惠金额
 * @property string $paid_amount 实际支付金额
 * @property string $paid_currency 支付货币代号
 */
class OrderAccount extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('order_account');
    }
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
                
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id'], 'required'],
            [['order_id', 'merchant_id'], 'integer'],
            [['order_amount', 'goods_amount', 'discount_amount', 'goods_discount', 'order_discount', 'pay_amount', 'refund_amount', 'shipping_fee', 'tax_fee', 'safe_fee', 'other_fee', 'exchange_rate', 'coupon_amount', 'card_amount', 'paid_amount'], 'number'],
            [['currency', 'paid_currency'], 'string', 'max' => 3],
            [['order_id'], 'unique'],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'order_id' => '订单ID',
            'merchant_id' => '商户ID',
            'order_amount' => '订单总金额',
            'goods_amount' => '商品总金额',
            'discount_amount' => '总优惠金额',
            'goods_discount' => '商品优惠价金额',
            'order_discount' => '订单优惠金额',
            'pay_amount' => '订单应付金额',
            'refund_amount' => '退款金额',
            'shipping_fee' => '运费',
            'tax_fee' => '税费',
            'safe_fee' => '保险费',
            'other_fee' => '附加费',
            'exchange_rate' => '汇率',
            'currency' => '货币代号',
            'coupon_amount' => '优惠券优惠金额',
            'card_amount' => '优惠券优惠金额',
            'paid_amount' => '实际支付金额',
            'paid_currency' => '支付货币代号',
        ];
    }    
}
