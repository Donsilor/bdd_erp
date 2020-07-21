<?php

namespace addons\Style\common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "style_diamond_log".
 *
 * @property int $id 主键
 * @property int $diamond_id 裸钻id
 * @property string $cert_id 证书号
 * @property int $log_type 操作类型
 * @property string $log_msg 文字描述
 * @property int $log_time 处理时间
 * @property string $creator 操作人
 * @property int $creator_id
 * @property int $created_at 创建时间
 */
class DiamondLog extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName("diamond_log");
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['diamond_id', 'creator_id'], 'required'],
            [['diamond_id', 'log_type', 'creator_id', 'created_at'], 'integer'],
            [['cert_id', 'creator'], 'string', 'max' => 30],
            [['log_msg'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键',
            'diamond_id' => '裸钻id',
            'cert_id' => '证书号',
            'log_type' => '操作类型',
            'log_msg' => '文字描述',
            'creator' => '操作人',
            'creator_id' => 'Creator ID',
            'created_at' => '创建时间',
        ];
    }

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
