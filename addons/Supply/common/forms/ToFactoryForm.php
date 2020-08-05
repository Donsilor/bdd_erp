<?php

namespace addons\Supply\common\forms;
use addons\Supply\common\models\Produce;
use Yii;

use yii\base\Model;


/**
 * 布产-分配工厂 Form
 *
 * @property string $attr_require 必填属性
 * @property string $attr_custom 选填属性
 */
class ToFactoryForm extends Produce
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
                [['supplier_id','follower_id'], 'required'],
           ];
        return array_merge(parent::rules() , $rules);
    }


    
}
