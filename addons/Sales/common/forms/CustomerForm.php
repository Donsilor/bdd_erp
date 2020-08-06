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
            [
                 ['mobile'], 'required','isEmpty'=>function($value){
                    if($this->channel_id != 3 && empty($value)){
                        return true;
                    }
                    return false;
                 },
                'whenClient' => "function (attribute, value) {
                         if($('#customerform-channel_id').val() != 3){
                              return true;//启用 必填 功能
                         }
                        return false;//禁用  必填 功能
                 }"
            ],
            [
                ['email'], 'required','isEmpty'=>function($value){
                    if($this->channel_id == 3 && empty($value)){
                        return true;
                    }
                    return false;
                },
                'whenClient' => "function (attribute, value) {
                         if($('#customerform-channel_id').val() == 3){
                              return true;//启用 必填 功能
                         }
                        return false;//禁用  必填 功能
                }"
            ],
            //[['firstname', 'lastname', 'mobile'], 'required'],
            [['realname', 'mobile', 'email', 'invoice_email'], 'filter', 'filter' => 'trim'],
            //['mobile', 'match', 'pattern'=>'/^[1][34578][0-9]{9}$/'],
            [['email', 'invoice_email'], 'email'],
            //[['email', 'mobile'], 'validateCustomer'],
        ];
        return ArrayHelper::merge(parent::rules() , $rules);
    }
    /**
     * 验证客户信息
     * @param unknown $attribute
     * @param unknown $params
     */
    public function validateCustomer($attribute,$params)
    {
        $this->addError("mobile","[非国际批发]客户手机号码必填222");
        return ;
        /* if($this->channel_id == 3) {
            //国际批发
            if(empty($this->email)) {
                $this->addError($attribute,"[国际批发]客户邮箱必填");
                return;
            }
        }else{
            //国际批发
            if(empty($this->mobile)) {
                $this->addError($attribute,"[非国际批发]客户手机号码必填");
                return;
            }
        } */
        
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
