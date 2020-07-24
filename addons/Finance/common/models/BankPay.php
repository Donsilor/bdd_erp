<?php

namespace addons\Finance\common\models;

use addons\Shop\common\models\BaseModel;
use common\models\backend\Member;
use common\models\common\Department;
use Yii;

/**
 * This is the model class for table "finance_bank_pay".
 *
 * @property int $id
 * @property string $finance_no 单号
 * @property int $dept_id 所属部门
 * @property string $apply_user 填单人
 * @property int $apply_time 填单时间
 * @property int $project_name 所属项目
 * @property string $budget_year 预算年度
 * @property int $budget_type 预算类型
 * @property string $pay_amount 支付金额（小写）
 * @property string $payee_company 收款单位
 * @property string $payee_account 收款账号
 * @property string $currency 币种
 * @property string $payee_bank 收款开户行
 * @property string $usage 用途
 * @property int $auditor_id 审核人ID
 * @property int $audit_status 审核状态
 * @property int $audit_time 审核时间
 * @property string $audit_remark 审核备注
 * @property int $creator_id 创建人Id
 * @property int $created_at 创建时间
 * @property int $updated_at
 */
class BankPay extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('finance_bank_pay');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['finance_no', 'dept_id', 'apply_user', 'apply_time', 'budget_year', 'budget_type', 'pay_amount', 'payee_company', 'payee_account', 'currency', 'payee_bank', 'usage'], 'required'],
            [['dept_id', 'apply_time', 'project_name', 'budget_type', 'auditor_id', 'audit_status', 'audit_time','finance_status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['pay_amount'], 'number'],
            [['usage'], 'string'],
            [['finance_no', 'apply_user', 'budget_year'], 'string', 'max' => 30],
            [['payee_company', 'payee_account', 'payee_bank'], 'string', 'max' => 50],
            [['currency'], 'string', 'max' => 3],
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
            'finance_no' => '单号',
            'dept_id' => '所属部门',
            'apply_user' => '填单人',
            'apply_time' => '填单时间',
            'project_name' => '所属项目',
            'budget_year' => '预算年度',
            'budget_type' => '预算类型',
            'pay_amount' => '支付金额（小写）',
            'payee_company' => '收款单位',
            'payee_account' => '收款账号',
            'currency' => '币种',
            'payee_bank' => '收款开户行',
            'usage' => '用途',
            'auditor_id' => '审核人ID',
            'audit_status' => '审核状态',
            'audit_time' => '审核时间',
            'audit_remark' => '审核备注',
            'finance_status' => '单据状态',
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
    /**
     * 部门
     * @return \yii\db\ActiveQuery
     */
    public function getDepartment()
    {
        return $this->hasOne(Department::class, ['id'=>'dept_id'])->alias('department');
    }
}
