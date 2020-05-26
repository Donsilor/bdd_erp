<?php

namespace addons\Warehouse\common\models;

use Yii;

/**
 * This is the model class for table "warehouse_bill_repair_log".
 *
 * @property int $id
 * @property int $repair_id 维修单ID
 * @property int $log_type 操作类型
 * @property string $log_msg 日志信息
 * @property int $creator_id 操作人
 * @property string $creator
 * @property int $created_at 操作时间
 */
class WarehouseBillRepairLog extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_bill_repair_log');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['repair_id', 'log_msg', 'creator_id', 'created_at'], 'required'],
            [['repair_id', 'log_type', 'creator_id', 'created_at'], 'integer'],
            [['log_msg'], 'string', 'max' => 255],
            [['creator'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'repair_id' => '维修单ID',
            'log_type' => '操作类型',
            'log_msg' => '日志信息',
            'creator_id' => '操作人',
            'creator' => 'Creator',
            'created_at' => '操作时间',
        ];
    }
}
