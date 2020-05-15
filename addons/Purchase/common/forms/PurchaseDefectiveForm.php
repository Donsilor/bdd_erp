<?php

namespace addons\Purchase\common\forms;

use Yii;

use addons\Purchase\common\models\PurchaseDefective;
/**
 * 采购收货单审核 Form
 *
 * @property string $attr_require 必填属性
 * @property string $attr_custom 选填属性
 */
class PurchaseDefectiveForm extends PurchaseDefective
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'supplier_id', 'defective_num', 'auditor_id', 'audit_time', 'audit_status', 'sort', 'status', 'creator_id', 'created_at', 'updated_at'], 'integer'],
            [['supplier_id', 'receipt_no'], 'required'],
            [['total_cost'], 'number'],
            [['receipt_no'], 'string', 'max' => 30],
            [['audit_remark', 'remark'], 'string', 'max' => 255],
        ];
    }   
    
}
