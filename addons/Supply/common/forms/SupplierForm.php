<?php

namespace addons\Supply\common\forms;

use Yii;

use addons\Supply\common\models\Supplier;
/**
 * 供应商 Form
 *
 * @property string $attr_require 必填属性
 * @property string $attr_custom 选填属性
 */
class SupplierForm extends Supplier
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'merchant_id', 'balance_type', 'auditor_id', 'audit_status', 'audit_time', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['supplier_name', 'business_no'], 'required'],
            ['supplier_name', 'match', 'pattern' => '/[^a-z\d\x{4e00}-\x{9fa5}\(\)]/ui', 'message'=>'只能填写字母数字汉字和小括号'],
            [['supplier_code', 'bank_account', 'bank_account_name', 'contactor', 'telephone', 'mobile', 'bdd_contactor', 'bdd_mobile', 'bdd_telephone'], 'string', 'max' => 30],
            ['supplier_code', 'unique'],
            [['supplier_name', 'business_address', 'address'], 'string', 'max' => 120],
            [['business_no', 'tax_no'], 'string', 'max' => 50],
            [['business_scope'], 'parseBusinessScope'],
            [['pay_type'], 'PayTypeScope'],
            [['contract_file', 'business_file', 'tax_file', 'bank_name'], 'string', 'max' => 100],
            [['audit_remark', 'remark'], 'string', 'max' => 255],
        ];
    }

}
