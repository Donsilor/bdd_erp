<?php

namespace addons\Finance\common\models;

use addons\Shop\common\models\BaseModel;
use Yii;

/**
 * This is the model class for table "finance_borrow_pay".
 *
 * @property int $id
 * @property int $dept_id 所属部门
 * @property int $apply_time 填单时间
 * @property string $apply_user 借款人
 * @property string $borrow_remark 借款事由
 * @property string $borrow_amount 借款金额
 * @property int $repay_time 预计还款时间
 * @property int $auditor_id 审核人ID
 * @property int $audit_status 审核状态
 * @property int $audit_time 审核时间
 * @property string $audit_remark 审核备注
 * @property int $creator_id 创建人Id
 * @property int $created_at 创建时间
 * @property int $updated_at
 */
class BorrowPay extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('finance_borrow_pay');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dept_id', 'apply_time'], 'required'],
            [['dept_id', 'apply_time', 'repay_time', 'auditor_id', 'audit_status', 'audit_time', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['borrow_remark'], 'string'],
            [['borrow_amount'], 'number'],
            [['apply_user'], 'string', 'max' => 30],
            [['audit_remark'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dept_id' => '所属部门',
            'apply_time' => '填单时间',
            'apply_user' => '借款人',
            'borrow_remark' => '借款事由',
            'borrow_amount' => '借款金额',
            'repay_time' => '预计还款时间',
            'auditor_id' => '审核人ID',
            'audit_status' => '审核状态',
            'audit_time' => '审核时间',
            'audit_remark' => '审核备注',
            'creator_id' => '创建人Id',
            'created_at' => '创建时间',
            'updated_at' => 'Updated At',
        ];
    }
}
