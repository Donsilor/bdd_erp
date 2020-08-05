<?php

namespace addons\Sales\common\models;

use Yii;
use \common\models\backend\Member;

/**
 * This is the model class for table "sales_freight".
 *
 * @property int $id ID
 * @property string $freight_no 快递单号
 * @property int $express_id 物流公司ID
 * @property string $consignee 收货人
 * @property string $consignee_address 收货人地址
 * @property string $consignee_mobile 收货人手机号
 * @property string $consignee_tel 收货人电话
 * @property string $consigner 发货人
 * @property string $order_sn 订单号
 * @property string $order_amount 订单总金额
 * @property int $sale_channel_id 销售渠道
 * @property string $out_trade_no 外部订单号
 * @property int $print_status 打印状态 0未打印 1已打印
 * @property int $print_num 打印次数
 * @property int $print_time 最新打印时间
 * @property string $remark 备注
 * @property int $status 状态 1启用 0禁用 -1删除
 * @property int $creator_id 创建人
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 */
class Freight extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('freight');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['freight_no', 'express_id'], 'required'],
            [['freight_no'], 'unique'],
            [['express_id', 'sale_channel_id', 'print_status', 'print_num', 'print_time', 'status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['order_amount'], 'number'],
            [['freight_no'], 'string', 'max' => 30],
            [['consignee', 'consignee_mobile', 'consignee_tel', 'consigner', 'order_sn'], 'string', 'max' => 20],
            [['consignee_address', 'remark'], 'string', 'max' => 255],
            [['out_trade_no'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'freight_no' => '快递单号',
            'express_id' => '快递公司',
            'consignee' => '收货人',
            'consignee_address' => '收货人地址',
            'consignee_mobile' => '收货人手机号',
            'consignee_tel' => '收货人电话',
            'consigner' => '发货人',
            'order_sn' => '订单号',
            'order_amount' => '订单总金额',
            'sale_channel_id' => '销售渠道',
            'out_trade_no' => '外部订单号',
            'print_status' => '打印状态',
            'print_num' => '打印次数',
            'print_time' => '最新打印时间',
            'remark' => '备注',
            'status' => '状态',
            'creator_id' => '创建人',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
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
     * 对应创建人模型
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(Member::class, ['id'=>'creator_id']);
    }

    /**
     * 对应销售渠道模型
     * @return \yii\db\ActiveQuery
     */
    public function getSaleChannel()
    {
        return $this->hasOne(SaleChannel::class, ['id'=>'sale_channel_id']);
    }
}
