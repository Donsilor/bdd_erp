<?php

namespace addons\Purchase\common\models;

use Faker\Provider\Base;
use Yii;

/**
 * This is the model class for table "purchase_defective_log".
 *
 * @property int $id ID
 * @property int $defective_id 采购收货单ID
 * @property string $defective_no 采购单收货单编号
 * @property int $log_type 操作类型
 * @property string $log_msg 文字描述
 * @property int $log_time 处理时间
 * @property string $log_module 操作模块
 * @property string $creator 操作人
 * @property int $creator_id
 * @property int $created_at 创建时间
 */
class PurchaseDefectiveLog extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('purchase_defective_log');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['defective_id', 'log_time', 'log_module', 'creator_id'], 'required'],
            [['defective_id', 'log_type', 'log_time', 'creator_id', 'created_at'], 'integer'],
            [['defective_no', 'log_module', 'creator'], 'string', 'max' => 30],
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
            'defective_id' => '不良返厂单ID',
            'defective_no' => '不良返厂单编号',
            'log_type' => '操作类型',
            'log_msg' => '文字描述',
            'log_time' => '处理时间',
            'log_module' => '操作模块',
            'creator' => '操作人',
            'creator_id' => 'Creator ID',
            'created_at' => '创建时间',
        ];
    }
}
