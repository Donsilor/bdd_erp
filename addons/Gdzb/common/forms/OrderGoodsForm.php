<?php

namespace addons\Gdzb\common\forms;

use addons\Gdzb\common\models\OrderGoods;
use addons\Style\common\enums\QibanTypeEnum;
use addons\Style\common\models\AttributeSpec;
use addons\Supply\common\enums\PeiliaoTypeEnum;
use common\enums\ConfirmEnum;
use common\enums\InputTypeEnum;
use Yii;
use common\helpers\ArrayHelper;

/**
 * 订单 Form
 */
class OrderGoodsForm extends OrderGoods
{

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

            ]);
    }



}
