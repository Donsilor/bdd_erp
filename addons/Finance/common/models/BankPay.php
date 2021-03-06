<?php

namespace addons\Finance\common\models;

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
        return self::tableFullName('bank_pay');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[ 'dept_id', 'apply_user', 'budget_year', 'budget_type', 'pay_amount', 'payee_company', 'payee_account', 'currency', 'payee_bank', 'usage'], 'required'],
            [['dept_id', 'project_name', 'budget_type', 'auditor_id', 'audit_status', 'audit_time','finance_status', 'creator_id', 'created_at', 'updated_at','flow_id'], 'integer'],
            [['pay_amount'], 'number'],
            [['usage'], 'string'],
            [['finance_no', 'apply_user', 'budget_year','oa_no'], 'string', 'max' => 30],
            [['payee_company', 'payee_account', 'payee_bank'], 'string', 'max' => 50],
            [['currency'], 'string', 'max' => 3],
            [['audit_remark','flow_ids'], 'string', 'max' => 255],
            [['annex_file'], 'string', 'max' => 500],
            [['annex_file'],'parseAnnexFile'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'oa_no' => 'OA单号',
            'finance_no' => '单号',
            'dept_id' => '所属部门',
            'apply_user' => '填单人',
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
            'flow_ids' => '关联列表',
            'flow_id' => '流程ID',
            'annex_file' => '附件',
            'creator_id' => '创建人Id',
            'created_at' => '创建时间',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * 单据附件
     */
    public function parseAnnexFile()
    {
        $annex_file = $this->annex_file;
        if(is_array($annex_file)){
            $this->annex_file = implode(',',$annex_file);
        }
    }

    function beforeValidate()
    {
        $annex_file = $this->annex_file;
        if(is_array($annex_file)){
            $this->annex_file = implode(',',$annex_file);
        }
        return parent::beforeValidate(); // TODO: Change the autogenerated stub
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
