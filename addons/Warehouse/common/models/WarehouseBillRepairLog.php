<?php

namespace addons\Warehouse\common\models;

use common\models\backend\Member;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

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
            [['repair_id', 'log_msg'], 'required'],
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
    /**
     * @param
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
    /**
     * @param bool $insert
     * @return bool
     * @throws \yii\base\Exception
     */
    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->creator_id = \Yii::$app->user->identity->getId();
            $this->creator = \Yii::$app->user->identity->username;
        }
        return parent::beforeSave($insert);
    }
    /**
     * 关联管理员一对一
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Member::class, ['id'=>'creator_id']);
    }
}
