<?php

namespace addons\Purchase\common\forms;

use Yii;
use common\helpers\ArrayHelper;
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
}
