<?php

namespace addons\Sales\common\forms;

use Yii;
use addons\Sales\common\models\SalesReturnGoods;
use common\helpers\StringHelper;
use common\helpers\ArrayHelper;

/**
 * 退款单明细 Form
 */
class ReturnGoodsForm extends SalesReturnGoods
{
    public $ids;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [

        ];
        return ArrayHelper::merge(parent::rules() , $rules);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        //合并
        return ArrayHelper::merge(parent::attributeLabels() , [
            'should_amount' => '实际成交价',
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

}
