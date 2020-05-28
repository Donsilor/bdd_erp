<?php

namespace addons\Purchase\common\forms;

use Yii;
use addons\Purchase\common\models\PurchaseReceiptGoods;
/**
 * 采购收货单明细 Form
 *
 */
class PurchaseReceiptGoodsForm extends PurchaseReceiptGoods
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [

        ];
        return array_merge(parent::rules() , $rules);
    }
    
}
