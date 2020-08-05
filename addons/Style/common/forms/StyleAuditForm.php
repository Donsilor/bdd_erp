<?php

namespace addons\Style\common\forms;

use Yii;

use addons\Style\common\models\Style;
/**
 * 款式审核 Form
 *
 * @property string $attr_require 必填属性
 * @property string $attr_custom 选填属性
 */
class StyleAuditForm extends Style
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
