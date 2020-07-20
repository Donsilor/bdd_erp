<?php

namespace addons\Supply\common\forms;
use addons\Supply\common\models\Produce;


/**
 * 布产-设置配料信息Form
 *
 */
class SetPeiliaoForm extends Produce
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
                [['peishi_type','peiliao_type'], 'required'],
           ];
        return array_merge(parent::rules() , $rules);
    }


    
}
