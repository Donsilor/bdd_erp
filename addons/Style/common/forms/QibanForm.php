<?php

namespace addons\Style\common\forms;

use addons\Style\common\models\Qiban;

/**
 * 起版-Form
 *
 *
 */
class QibanForm extends Qiban
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
                [['style_sex'], 'required'],
        ];
        return array_merge(parent::rules() , $rules);
    }        
}
