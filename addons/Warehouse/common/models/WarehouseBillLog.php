<?php

namespace addons\Warehouse\common\models;

use Yii;

/**
 * This is the model class for table "warehouse_bill_log".
 *
 * @property int $id ID
 * @property int $bill_id 布产Id
 * @property int $log_type 操作类型
 * @property string $log_msg 文字描述
 * @property int $bill_status 布产状态
 * @property string $log_module 操作模块
 * @property string $creator 操作人
 * @property int $creator_id
 * @property int $created_at 创建时间
 */
class WarehouseBillLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'warehouse_bill_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bill_id', 'log_module', 'creator_id'], 'required'],
            [['bill_id', 'log_type', 'bill_status', 'creator_id', 'created_at'], 'integer'],
            [['log_msg'], 'string', 'max' => 500],
            [['log_module', 'creator'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bill_id' => '布产Id',
            'log_type' => '操作类型',
            'log_msg' => '文字描述',
            'bill_status' => '布产状态',
            'log_module' => '操作模块',
            'creator' => '操作人',
            'creator_id' => 'Creator ID',
            'created_at' => '创建时间',
        ];
    }
}
