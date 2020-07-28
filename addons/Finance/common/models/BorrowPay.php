<?php

namespace addons\Finance\common\models;

use common\models\backend\Member;
use Yii;

/**
 * This is the model class for table "finance_borrow_pay".
 *
 * @property int $id
 * @property string $finance_no 单号
 * @property int $dept_id 所属部门
 * @property int $apply_time 填单时间
 * @property string $apply_user 借款人
 * @property string $borrow_remark 借款事由
 * @property string $borrow_amount 借款金额
 * @property string $currency 币种
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
        return self::tableFullName('borrow_pay');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['finance_no', 'dept_id', 'apply_time', 'apply_user', 'borrow_remark', 'borrow_amount', 'currency', 'repay_time'], 'required'],
            [['dept_id', 'repay_time', 'auditor_id', 'audit_status', 'audit_time','finance_status', 'creator_id', 'created_at', 'updated_at','flow_id'], 'integer'],
            [['borrow_remark'], 'string'],
            [['borrow_amount'], 'number'],
            [['finance_no', 'apply_user'], 'string', 'max' => 30],
            [['currency'], 'string', 'max' => 3],
            [['audit_remark','flow_ids'], 'string', 'max' => 255],
            [['annex_file'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'finance_no' => '单号',
            'dept_id' => '所属部门',
            'apply_user' => '借款人',
            'borrow_remark' => '借款事由',
            'borrow_amount' => '借款金额',
            'currency' => '币种',
            'repay_time' => '预计还款时间',
            'auditor_id' => '审核人ID',
            'audit_status' => '审核状态',
            'audit_time' => '审核时间',
            'audit_remark' => '审核备注',
            'finance_status' => '单据状态',
            'flow_id' => '流程ID',
            'flow_ids' => '关联列表',
            'annex_file' => '附件',
            'creator_id' => '创建人Id',
            'created_at' => '创建时间',
            'updated_at' => 'Updated At',
        ];
    }


    /**
     * 创建人
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(Member::class, ['id'=>'creator_id'])->alias('creator');
    }
    /**
     * 审核人
     * @return \yii\db\ActiveQuery
     */
    public function getAuditor()
    {
        return $this->hasOne(Member::class, ['id'=>'auditor_id'])->alias('auditor');
    }
}
