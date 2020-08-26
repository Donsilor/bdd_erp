<?php

namespace addons\Sales\common\models;

use Yii;

/**
 * This is the model class for table "sales_return_log".
 *
 * @property int $id 主键
 * @property int $return_id 退款id
 * @property int $log_type 操作类型
 * @property string $log_msg 文字描述
 * @property string $creator 操作人
 * @property int $creator_id
 * @property int $created_at 创建时间
 */
class SalesReturnLog extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('return_log');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['return_id', 'creator_id'], 'required'],
            [['return_id', 'log_type', 'creator_id', 'created_at'], 'integer'],
            [['log_msg'], 'string', 'max' => 255],
            [['creator'], 'string', 'max' => 30],
            [['return_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'return_id' => '退款id',
            'log_type' => '操作类型',
            'log_msg' => '文字描述',
            'creator' => '操作人',
            'creator_id' => 'Creator ID',
            'created_at' => '创建时间',
        ];
    }
}
