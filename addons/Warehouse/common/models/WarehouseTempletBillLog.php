<?php

namespace addons\Warehouse\common\models;

use Yii;

/**
 * This is the model class for table "warehouse_templet_bill_log".
 *
 * @property int $id ID
 * @property int $bill_id 单据ID
 * @property int $bill_status 单据状态
 * @property int $log_type 操作类型
 * @property string $log_msg 文字描述
 * @property string $log_module 操作模块
 * @property string $creator 操作人
 * @property int $creator_id 操作人ID
 * @property int $created_at 操作时间
 */
class WarehouseTempletBillLog extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_templet_bill_log');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['bill_id', 'log_module', 'creator_id'], 'required'],
            [['bill_id', 'bill_status', 'log_type', 'creator_id', 'created_at'], 'integer'],
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
            'bill_id' => '单据ID',
            'bill_status' => '单据状态',
            'log_type' => '操作类型',
            'log_msg' => '文字描述',
            'log_module' => '操作模块',
            'creator' => '操作人',
            'creator_id' => '操作人ID',
            'created_at' => '操作时间',
        ];
    }
}
