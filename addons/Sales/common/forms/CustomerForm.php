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
            [['realname', 'channel_id'], 'required'],
            //[['firstname', 'lastname', 'mobile'], 'required'],
            [['realname', 'mobile', 'email', 'invoice_email'], 'filter', 'filter' => 'trim'],
            //['mobile', 'match', 'pattern'=>'/^[1][34578][0-9]{9}$/'],
            [['email', 'invoice_email'], 'email'],
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