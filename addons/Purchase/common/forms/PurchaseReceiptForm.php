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
    public $ids;
    public $produce_sns;
    public $goods;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['put_in_type'], 'required'],
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
            'put_in_type'=>'采购方式',
            'to_warehouse_id'=>'入库仓库',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getIds(){
        if($this->ids){
            return StringHelper::explode($this->ids);
        }
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getProduceSns()
    {
        return StringHelper::explodeIds($this->produce_sns);
    }
}
