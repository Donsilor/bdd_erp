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
    public $goods;
    public $produce_sns;
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
            'supplier_id' => '工厂名称',
            'put_in_type'=>'采购方式',
            'creator_id' => '制单人',
            'created_at' => '制单时间',
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
    /**
     * {@inheritdoc}
     */
    public function getGoods()
    {
        if($this->goods
            && $this->goods['goods'][0]['produce_sn']){
            return $this->goods['goods']??[];
        }
        return [];
    }
}
