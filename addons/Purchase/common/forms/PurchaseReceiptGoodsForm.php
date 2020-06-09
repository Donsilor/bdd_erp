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
            'jintuo_type'=>'金托类型',
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
        $query = PurchaseReceiptGoods::find();
        $query->from(['rg'=> PurchaseReceiptGoods::tableName()]);
        $query->leftJoin(['r' => PurchaseReceipt::tableName()], 'r.id = rg.receipt_id');
        $query->where(['rg.id' => $ids]);
        $query->distinct($col);
        $num = $query->count(1);
        return $num==1?:0;
    }
}
