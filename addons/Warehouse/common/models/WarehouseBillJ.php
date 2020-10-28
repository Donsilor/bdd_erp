<?php

namespace addons\Warehouse\common\models;

use Yii;
use common\models\backend\Member;

/**
 * This is the model class for table "warehouse_bill_j".
 *
 * @property int $id 单据ID
 * @property int $lender_id 借货人
 * @property int $receive_id 接收人
 * @property int $receive_time 接收时间
 * @property string $receive_remark 接收备注
 * @property int $restore_num 还货数量
 * @property int $est_restore_time 预计还货时间
 * @property int $rel_restore_time 实际还货时间
 * @property int $lend_status 借货状态
 */
class WarehouseBillJ extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('warehouse_bill_j');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'lender_id', 'receive_id', 'receive_time', 'restore_num', 'est_restore_time', 'rel_restore_time', 'lend_status'], 'integer'],
            [['receive_remark'], 'string', 'max' => 255],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '单据ID',
            'lender_id' => '借货人',
            'receive_id' => '接收人',
            'receive_time' => '接收时间',
            'receive_remark' => '接收备注',
            'restore_num' => '还货数量',
            'est_restore_time' => '最新还货时间',
            'rel_restore_time' => '实际还货时间',
            'lend_status' => '借货状态',
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [];
    }

    /**
     *
     * {@inheritDoc}
     * @see \yii\base\Model::afterValidate()
     */
    public function afterValidate()
    {
        if (!is_numeric($this->est_restore_time)) {
            $this->setAttribute('est_restore_time', $this->est_restore_time ? strtotime($this->est_restore_time) : 0);
        }
        return parent::beforeValidate(); // TODO: Change the autogenerated stub
    }

    /**
     * 借货人
     * @return \yii\db\ActiveQuery
     */
    public function getLender()
    {
        return $this->hasOne(Member::class, ['id' => 'lender_id'])->alias('lender');
    }
}
