<?php

namespace addons\Sales\common\models;

use Yii;

/**
 * This is the model class for table "sales_order_log".
 *
 * @property int $id ID
 * @property int $order_id 订单ID
 * @property string $order_sn 订单编号
 * @property int $order_status 订单状态
 * @property int $log_type 操作类型
 * @property string $log_msg 文字描述
 * @property int $log_time 处理时间
 * @property string $log_module 操作模块
 * @property string $creator 操作人
 * @property int $creator_id
 * @property int $created_at 创建时间
 */
class OrderLog extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('order_log');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'log_time', 'log_module', 'creator_id'], 'required'],
            [['order_id', 'order_status', 'log_type', 'log_time', 'creator_id', 'created_at'], 'integer'],
            [['order_sn', 'log_module', 'creator'], 'string', 'max' => 30],
            [['log_msg'], 'string', 'max' => 500],
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
            'order_sn' => '订单编号',
            'order_status' => '订单状态',
            'log_type' => '操作类型',
            'log_msg' => '文字描述',
            'log_time' => '处理时间',
            'log_module' => '操作模块',
            'creator' => '操作人',
            'creator_id' => '操作人',
            'created_at' => '创建时间',
        ];
    }
}
