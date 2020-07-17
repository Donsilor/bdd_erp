<?php

namespace addons\Shop\common\models;

use Yii;

/**
 * This is the model class for table "{{%order_account}}".
 *
 * @property int $order_id 订单ID
 * @property int $sync_platform 同步平台ID
 * @property int $sync_created 是否同步新订单
 * @property int $sync_created_time 同步新订单时间
 * @property int $sync_refund 是否同步退款
 * @property int $sync_refund_time 同步退款时间
 */
class OrderSync extends BaseModel
{
    
    public function behaviors()
    {
        return [];
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('order_sync');
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
                [['order_id','sync_platform'], 'required'],
                [['order_id', 'sync_platform','sync_created','sync_created_time','sync_refund','sync_refund_time'], 'integer'],
                [['order_id','sync_platform'], 'unique'],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
                'order_id' => '订单ID',
                'sync_platform' => '同步平台',
                'sync_created' => "是否同步订单",
                'sync_created_time' => "同步订单时间",
                'sync_refund' => "是否同步退款",
                'sync_refund_time' => "同步退款时间",                
        ];
    }
}
