<?php

namespace addons\Gdzb\common\forms;

use Yii;
use common\helpers\ArrayHelper;
use addons\Gdzb\common\models\Order;


/**
 * 订单 Form
 */
class OrderDeliveryForm extends Order
{

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['express_id','express_no','delivery_time'], 'required'],
            [['delivery_time'],'parseDeliveryTime']
        ];
        return ArrayHelper::merge(parent::rules(),$rules);
    }

    /**
     * 款式图库
     */
    public function parseDeliveryTime()
    {
        $this->delivery_time = strtotime($this->delivery_time);

        return $this->delivery_time;
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        //合并
        return ArrayHelper::merge(parent::attributeLabels() , [

        ]);
    }

}
