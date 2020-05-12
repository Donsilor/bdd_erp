<?php

namespace addons\Purchase\common\forms;

use Yii;

use addons\Purchase\common\models\PurchaseReceipt;
/**
 * 采购收货单审核 Form
 *
 * @property string $attr_require 必填属性
 * @property string $attr_custom 选填属性
 */
class PurchaseReceiptForm extends PurchaseReceipt
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
                [['id','status','audit_status','audit_time','auditor_id','updated_at'], 'integer'],
                [['audit_status'], 'required'],                
                [['audit_remark'],'string','max'=>255],
        ];
    }   
    
}
