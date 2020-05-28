<?php

namespace addons\Purchase\common\forms;

use Yii;
use addons\Purchase\common\models\PurchaseDefectiveGoods;
/**
 * 不良返厂单明细 Form
 *
 */
class PurchaseDefectiveGoodsForm extends PurchaseDefectiveGoods
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
