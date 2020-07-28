<?php

namespace addons\Purchase\common\models;

use common\models\backend\Member;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "purchase_receipt_log".
 *
 * @property int $id ID
 * @property int $receipt_id 采购收货单ID
 * @property string $receipt_no 采购单收货单编号
 * @property int $log_type 操作类型
 * @property string $log_msg 文字描述
 * @property string $log_module 操作模块
 * @property string $creator 操作人
 * @property int $creator_id
 * @property int $created_at 创建时间
 */
class PurchaseReceiptLog extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('purchase_receipt_log');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['receipt_id', 'log_module'], 'required'],
            [['receipt_id', 'log_type', 'creator_id', 'created_at','log_time'], 'integer'],
            [['receipt_no', 'log_module', 'creator'], 'string', 'max' => 30],
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
            'receipt_id' => '采购收货单ID',
            'receipt_no' => '采购单收货单编号',
            'log_type' => '操作类型',
            'log_msg' => '文字描述',
            'log_module' => '操作模块',
            'log_time' => '操作时间',
            'creator' => '操作人',
            'creator_id' => 'Creator ID',
            'created_at' => '创建时间',
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
