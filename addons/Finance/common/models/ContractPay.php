<?php

namespace addons\Finance\common\models;

use addons\Shop\common\models\BaseModel;
use Yii;

/**
 * This is the model class for table "finance_contract_pay".
 *
 * @property int $id
 * @property int $dept_id 所属部门
 * @property string $apply_user 填单人
 * @property int $apply_time 填单时间
 * @property string $contract_name 合同名称
 * @property string $contract_no 合同编号
 * @property string $contract_info 合同单位及其联系人、联系电话
 * @property int $contract_type 合同类型
 * @property int $payment_type 付款类型
 * @property string $amount_total 合同款总额
 * @property string $last_period_total 上期累计
 * @property string $this_period_amount 本期付款
 * @property string $this_period_total 本期累计
 * @property int $auditor_id 审核人ID
 * @property int $audit_status 审核状态
 * @property int $audit_time 审核时间
 * @property string $audit_remark 审核备注
 * @property int $created_at 创建时间
 * @property int $updated_at
 * @property int $creator_id 创建人Id
 * @property string $remark 备注
 */
class ContractPay extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return self::tableFullName('finance_contract_pay');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dept_id', 'apply_user', 'apply_time', 'contract_name', 'contract_info', 'contract_type', 'payment_type', 'amount_total', 'last_period_total', 'this_period_amount', 'this_period_total'], 'required'],
            [['dept_id', 'apply_time', 'contract_type', 'payment_type', 'auditor_id', 'audit_status', 'audit_time', 'created_at', 'updated_at', 'creator_id'], 'integer'],
            [['amount_total', 'last_period_total', 'this_period_amount', 'this_period_total'], 'number'],
            [['apply_user', 'contract_no'], 'string', 'max' => 30],
            [['contract_name'], 'string', 'max' => 50],
            [['contract_info', 'audit_remark', 'remark'], 'string', 'max' => 255],
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
            'apply_user' => '填单人',
            'apply_time' => '填单时间',
            'contract_name' => '合同名称',
            'contract_no' => '合同编号',
            'contract_info' => '合同单位及其联系人、联系电话',
            'contract_type' => '合同类型',
            'payment_type' => '付款类型',
            'amount_total' => '合同款总额',
            'last_period_total' => '上期累计',
            'this_period_amount' => '本期付款',
            'this_period_total' => '本期累计',
            'auditor_id' => '审核人ID',
            'audit_status' => '审核状态',
            'audit_time' => '审核时间',
            'audit_remark' => '审核备注',
            'created_at' => '创建时间',
            'updated_at' => 'Updated At',
            'creator_id' => '创建人Id',
            'remark' => '备注',
        ];
    }
}
