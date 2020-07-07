<?php

namespace addons\Sales\common\forms;

use Yii;
use addons\Sales\common\models\Salechannel;
use common\helpers\ArrayHelper;

/**
 * 销售渠道 Form
 */
class SaleChannelForm extends Salechannel
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            //[['realname'], 'required'],
            //[['firstname', 'lastname', 'mobile'], 'required'],
            //['mobile', 'filter', 'filter' => 'trim'],
            //['mobile', 'match', 'pattern'=>'/^[1][34578][0-9]{9}$/'],
            //['email', 'email'],
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
