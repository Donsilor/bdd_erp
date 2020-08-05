<?php

namespace addons\Supply\common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "supply_produce_log".
 *
 * @property int $id ID
 * @property int $produce_id 布产Id
 * @property string $produce_sn 布产编号
 * @property int $log_type 操作类型
 * @property string $log_msg 文字描述
 * @property int $log_time 处理时间
 * @property string $log_module 操作模块
 * @property string $creator 操作人
 * @property int $creator_id
 * @property int $created_at 创建时间
 */
class ProduceLog extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('produce_log');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['produce_id', 'log_time', 'log_module', 'creator_id'], 'required'],
            [['produce_id', 'log_type', 'log_time', 'creator_id','bc_status', 'created_at'], 'integer'],
            [['produce_sn', 'log_module', 'creator'], 'string', 'max' => 30],
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
            'produce_id' => '布产Id',
            'produce_sn' => '布产编号',
            'log_type' => '操作类型',
            'bc_status' => '布产状态',
            'log_msg' => '文字描述',
            'log_time' => '处理时间',
            'log_module' => '操作模块',
            'creator' => '操作人',
            'creator_id' => 'Creator ID',
            'created_at' => '创建时间',
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
        ];
    }
}
