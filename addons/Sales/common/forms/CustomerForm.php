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
            [['firstname', 'lastname', 'mobile'], 'required'],
            ['mobile', 'filter', 'filter' => 'trim'],
            ['mobile', 'match', 'pattern'=>'/^[1][34578][0-9]{9}$/'],
            ['email', 'email'],
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
