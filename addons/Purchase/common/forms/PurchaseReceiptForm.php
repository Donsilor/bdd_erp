<?php

namespace addons\Purchase\common\forms;


use Yii;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;
use addons\Purchase\common\models\PurchaseReceipt;
/**
 * 采购收货单审核 Form
 *
 */
class PurchaseReceiptForm extends PurchaseReceipt
{
    public $produce_sns;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [

        ];
        return array_merge(parent::rules() , $rules);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        //合并
        return ArrayHelper::merge(parent::attributeLabels() , [
            'produce_sns'=>'布产单号',
            'receipt_num'=>'数量',
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
