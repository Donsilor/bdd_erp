<?php

namespace addons\Warehouse\common\models;

use common\models\backend\Member;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "warehouse_stone_bill_log".
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
class WarehouseStoneBillLog extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_stone_bill_log');
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
            $this->creator_id = Yii::$app->user->identity->getId();
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
