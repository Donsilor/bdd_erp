<?php

namespace addons\Purchase\common\forms;

use Yii;
use common\helpers\ArrayHelper;
use common\helpers\StringHelper;
use addons\Purchase\common\models\PurchaseReceipt;
use addons\Purchase\common\models\PurchaseGoldReceiptGoods;
/**
 * 采购收货单明细 Form
 *
 */
class PurchaseGoldReceiptGoodsForm extends PurchaseGoldReceiptGoods
{
    public $ids;
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
            'id'=>'流水号',
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
    public function checkDistinct($col, $ids){
        $query = PurchaseGoldReceiptGoods::find();
        $query->from(['rg'=> PurchaseGoldReceiptGoods::tableName()]);
        $query->leftJoin(['r' => PurchaseReceipt::tableName()], 'r.id = rg.receipt_id');
        $query->where(['rg.id' => $ids]);
        $query->distinct($col);
        $query->count(1);
        return $query->one()==1?:0;
    }
}
