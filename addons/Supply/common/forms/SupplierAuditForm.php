<?php

namespace addons\Supply\common\forms;

use Yii;

use addons\Supply\common\models\Supplier;
/**
 * 供应商审核 Form
 *
 * @property string $attr_require 必填属性
 * @property string $attr_custom 选填属性
 */
class SupplierAuditForm extends Supplier
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
