<?php

namespace addons\Purchase\common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use addons\Purchase\common\models\BaseModel;
/**
 * This is the model class for table "apply_log".
 *
 * @property int $id 主键
 * @property int $apply_id 采购单id
 * @property string $apply_sn 采购单编号
 * @property int $log_type 操作类型
 * @property string $log_msg 文字描述
 * @property int $log_time 处理时间
 * @property string $log_module 操作模块
 * @property string $creator 操作人
 * @property int $creator_id
 * @property int $created_at 创建时间
 */
class PurchaseApplyLog extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('purchase_apply_log');
    }
    /**
     * 重置 behaviors
     * {@inheritDoc}
     * @see \yii\base\Component::behaviors()
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
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
                [['apply_id', 'log_time', 'log_module', 'creator_id'], 'required'],
                [['apply_id', 'log_type', 'log_time', 'creator_id', 'created_at'], 'integer'],
                [['apply_sn', 'log_module', 'creator'], 'string', 'max' => 30],
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
                'apply_id' => '采购单id',
                'apply_sn' => '采购单编号',
                'log_type' => '操作类型',
                'log_msg' => '文字描述',
                'log_time' => '处理时间',
                'log_module' => '操作模块',
                'creator' => '操作人',
                'creator_id' => '操作人ID',
                'created_at' => '创建时间',
        ];
    }
}
