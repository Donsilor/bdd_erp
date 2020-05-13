<?php

namespace addons\Purchase\common\forms;

use addons\Purchase\common\models\Purchase;

/**
 * 分配跟单人 Form
 *
 * @property string $follower_id 必填属性
 */
class PurchaseFollowerForm extends Purchase
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
                [['follower_id'], 'required'],
        ];
    }   
    
}
