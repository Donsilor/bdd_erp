<?php

namespace addons\Purchase\common\models;

use Yii;

/**
 * This is the model class for table "purchase_log".
 *
 * @property int $id 主键
 * @property int $purchase_id 采购单id
 * @property string $purchase_sn 采购单编号
 * @property int $log_type 操作类型
 * @property string $log_msg 文字描述
 * @property int $log_time 处理时间
 * @property string $log_module 操作模块
 * @property string $creator 操作人
 * @property int $creator_id
 * @property int $created_at 创建时间
 */
class PurchaseLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'purchase_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['purchase_id', 'log_time', 'log_module', 'creator_id'], 'required'],
            [['purchase_id', 'log_type', 'log_time', 'creator_id', 'created_at'], 'integer'],
            [['purchase_sn', 'log_module', 'creator'], 'string', 'max' => 30],
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
            'purchase_id' => '采购单id',
            'purchase_sn' => '采购单编号',
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
