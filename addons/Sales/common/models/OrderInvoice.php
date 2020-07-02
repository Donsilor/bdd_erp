<?php

namespace addons\Sales\common\models;

use Yii;

/**
 * This is the model class for table "sales_order_invoice".
 *
 * @property int $id
 * @property int $order_id 订单ID
 * @property int $invoice_type 发票类型：1=企业，2=个人
 * @property string $invoice_title 发票抬头
 * @property string $tax_number 纳税人识别号
 * @property int $is_electronic 是否电子发票：0=不是，1=是
 * @property string $email 发票邮箱
 * @property int $send_num 发送次数
 * @property int $created_at 创建时间
 * @property int $updated_at 修改时间
 */
class OrderInvoice extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('order_invoice');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id'], 'required'],
            [['order_id', 'invoice_type', 'is_electronic', 'send_num', 'created_at', 'updated_at'], 'integer'],
            [['invoice_title'], 'string', 'max' => 80],
            [['tax_number'], 'string', 'max' => 50],
            [['email'], 'string', 'max' => 60],
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
            'invoice_type' => '发票类型',
            'invoice_title' => '发票抬头',
            'tax_number' => '纳税人识别号',
            'is_electronic' => '是否电子发票',
            'email' => '发票邮箱',
            'send_num' => '发送次数',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }
}
