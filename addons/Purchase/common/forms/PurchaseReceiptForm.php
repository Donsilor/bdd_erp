<?php

namespace addons\Purchase\common\forms;


use Yii;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;
use addons\Purchase\common\models\PurchaseReceipt;
/**
 * 采购收货单审核 Form
 *
 * @property string $attr_require 必填属性
 * @property string $attr_custom 选填属性
 */
class PurchaseReceiptForm extends PurchaseReceipt
{
    public $produce_sns;
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

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        //合并
        return ArrayHelper::merge(parent::attributeLabels() , [
            'produce_sns'=>'布产单号'
        ]);
    }

    /**
     * 批量获取布产单号
     */
    public function getProduceSns()
    {
        return StringHelper::explodeIds($this->produce_sns);
    }
}
