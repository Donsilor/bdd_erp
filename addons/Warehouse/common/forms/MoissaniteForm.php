<?php

namespace addons\Warehouse\common\forms;

use Yii;
use addons\Warehouse\common\models\Moissanite;
use common\helpers\ArrayHelper;

/**
 * 莫桑石列表 Form
 */
class MoissaniteForm extends Moissanite
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['style_sn'], 'required']
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
