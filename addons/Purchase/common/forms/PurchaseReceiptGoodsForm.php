<?php

namespace addons\Purchase\common\forms;

use common\helpers\StringHelper;
use Yii;
use common\helpers\ArrayHelper;
use addons\Purchase\common\models\PurchaseReceipt;
use addons\Purchase\common\models\PurchaseReceiptGoods;
/**
 * 采购收货单明细 Form
 *
 */
class PurchaseReceiptGoodsForm extends PurchaseReceiptGoods
{
    public $ids;
    public $remark;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['remark'], 'string', 'max'=>255],
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
            'id'=>'流水号',
            'jintuo_type'=>'金托类型',
            'remark'=>'备注',
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
    public function getGoodsView(){
        $label = $this->attributeLabels();
        $data = [];
        foreach ($this->toArray() as $k => $item) {
            $data[$label[$k]] = $item;
        }
        return $data;
    }
}
