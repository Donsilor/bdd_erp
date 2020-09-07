<?php

namespace addons\Style\common\forms;

use Yii;
use addons\Style\common\models\Style;

/**
 * 款式 Form
 *
 */
class StyleForm extends Style
{
    public $file;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['file'], 'file', 'extensions' => ['csv']],//'skipOnEmpty' => false,
        ];
    }   
    
}
