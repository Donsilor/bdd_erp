<?php

namespace addons\Supply\common\forms;

use addons\Supply\common\models\Produce;
use common\helpers\ArrayHelper;
use Yii;

use addons\Supply\common\models\Supplier;
/**
 * 供应商审核 Form
 *
 * @property string $attr_require 必填属性
 * @property string $attr_custom 选填属性
 */
class ProduceFollowerForm extends Produce
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules =  [
                [['apply_follower_id'], 'required'],

        ];
        return ArrayHelper::merge(parent::rules() , $rules);
    }   
    
}
