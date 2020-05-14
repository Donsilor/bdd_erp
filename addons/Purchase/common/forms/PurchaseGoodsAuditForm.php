<?php

namespace addons\Purchase\common\forms;

use Yii;
use yii\base\Model;

/**
 * 采购商品申请编辑 Form
 *
 * @property string $attr_require 必填属性
 * @property string $attr_custom 选填属性
 */
class PurchaseGoodsAuditForm extends Model
{
    public $id;
    public $audit_status;
    public $audit_remark;
    public $model;
    public function rules()
    {
        return [
                [['audit_status'], 'required'],
                [['id','audit_status'], 'integer'],
                [['audit_remark'], 'string', 'max' => 255],
        ];
    }
    
    public function attributeLabels()
    {
        return [
                'id' => 'ID',
                'audit_status' => '审核状态',                
                'audit_remark' => '审核备注',                
        ];
    }
    
}
