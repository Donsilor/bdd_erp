<?php

namespace addons\Gdzb\common\forms;

use addons\Gdzb\common\enums\InvoiceStatusEnum;
use addons\Gdzb\common\models\RefundGoods;
use addons\Sales\common\enums\InvoiceTitleTypeEnum;
use addons\Sales\common\enums\InvoiceTypeEnum;
use common\models\common\Country;
use Yii;
use common\helpers\ArrayHelper;
use addons\Gdzb\common\models\Order;


/**
 * 订单 Form
 */
class RefundGoodsForm extends RefundGoods
{

    public $factory_remark;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
        ];
        return ArrayHelper::merge(parent::rules(),$rules);
    }    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        //合并
        return ArrayHelper::merge(parent::attributeLabels() , [
            'factory_remark' => '备注'
        ]);
    }


}
