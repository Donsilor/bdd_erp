<?php

namespace addons\Sales\common\forms;

use Yii;
use addons\Sales\common\models\Customer;
use common\helpers\ArrayHelper;

/**
 * 会员管理 Form
 */
class CustomerForm extends Customer
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            //[['supplier_code','supplier_name','supplier_tag'], 'unique'],
            //[['supplier_name'], 'match', 'pattern' => '/[^a-z\d\x{4e00}-\x{9fa5}\(\)]/ui', 'message'=>'只能填写字母数字汉字和小括号'],
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
