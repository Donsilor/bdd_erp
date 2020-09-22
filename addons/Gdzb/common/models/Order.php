<?php

namespace addons\Gdzb\common\models;

use addons\Sales\common\models\Express;
use addons\Sales\common\models\OrderAddress;
use addons\Sales\common\models\Payment;
use addons\Sales\common\models\SaleChannel;
use addons\Warehouse\common\models\Warehouse;
use common\models\backend\Member;
use Yii;

/**
 * This is the model class for table "gdzb_order".
 *
 * @property int $id ID
 * @property int $merchant_id 商户
 * @property string $language 订单语言
 * @property string $currency 货币
 * @property string $order_sn 订单编号
 * @property string $order_amount 订单金额
 * @property string $refund_no 订单金额
 * @property string $refund_amount 退款金额
 * @property int $channel_id 销售渠道
 * @property int $goods_num 商品数量
 * @property string $pay_sn 支付单号
 * @property int $pay_type 支付方式 0待支付 1微信 2支付宝 3对公账户 4其他
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
 * @property int $supplier_id 供应商ID
 * @property int $warehouse_id 所属仓库
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
            [['merchant_id', 'channel_id', 'goods_num', 'refund_num','pay_type', 'pay_status', 'pay_time', 'finished_time',
                'order_time', 'order_status', 'refund_status', 'express_id', 'delivery_status',
                'follower_id', 'followed_time', 'followed_status', 'audit_status', 'audit_time', 'auditor_id',
                'customer_id', 'supplier_id', 'warehouse_id', 'is_invoice', 'creator_id', 'created_at', 'updated_at',
                'collect_type','order_from'], 'integer'],
            [['order_amount', 'refund_amount','cost_price'], 'number'],
            [['language'], 'string', 'max' => 5],
            [['currency'], 'string', 'max' => 3],
            [['order_sn'], 'string', 'max' => 20],
            [['pay_sn'], 'string', 'max' => 32],
            [['express_no','collect_no','refund_no'], 'string', 'max' => 50],
            [['audit_remark', 'pay_remark', 'remark'], 'string', 'max' => 255],
            [['customer_name'], 'string', 'max' => 60],
            [['customer_mobile'], 'string', 'max' => 30],
            [['customer_weixin', 'customer_account'], 'string', 'max' => 120],
            [['customer_message'], 'string', 'max' => 500],
            [['consignee_info', 'invoice_info'], 'string', 'max' => 1000],
            [['order_sn'], 'unique'],
            [['delivery_time'], 'safe'],
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
            'order_amount' => '订单金额',
            'refund_amount' => '退款金额',
            'cost_price' => '成本价',
            'channel_id' => '销售渠道',
            'goods_num' => '商品数量',
            'refund_num' => '退款数量',
            'pay_sn' => '支付单号',
            'pay_type' => '支付方式',
            'pay_status' => '支付状态',
            'pay_time' => '付款时间',
            'collect_type' => '收款方式',
            'collect_no' => '收款账号',
            'finished_time' => '订单完成时间',
            'order_time' => '下单时间',
            'order_status' => '订单状态',
            'order_from' => '订单来源',
            'refund_status' => '退款状态',
            'express_id' => '快递公司',
            'express_no' => '快递单号',
            'refund_no' => '退款单号',
            'delivery_status' => '发货状态',
            'delivery_time' => '发货时间',
            'follower_id' => '所属客服',
            'followed_time' => '跟进时间',
            'followed_status' => '跟进状态',
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
            'supplier_id' => '供应商ID',
            'warehouse_id' => '所属仓库',
            'pay_remark' => '付款备注',
            'remark' => '订单备注',
            'is_invoice' => '是否开发票',
            'invoice_info' => '发票信息(json)',
            'creator_id' => '创建人',
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
                $this->follower_id = $this->creator_id;
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
        return $this->hasOne(\common\models\backend\Member::class, ['id'=>'follower_id'])->alias('follower');
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
     * 对应渠道
     * @return \yii\db\ActiveQuery
     */
    public function getSaleChannel()
    {
        return $this->hasOne(SaleChannel::class, ['id'=>'channel_id'])->alias('saleChannel');
    }

    /**
     * 对应仓库
     * @return \yii\db\ActiveQuery
     */
    public function getWarehouse()
    {
        return $this->hasOne(Warehouse::class, ['id'=>'warehouse_id'])->alias('warehouse');
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
     * 对应供应商
     * @return \yii\db\ActiveQuery
     */
    public function getSupplier()
    {
        return $this->hasOne(Supplier::class, ['id'=>'supplier_id'])->alias('supplier');
    }
}
