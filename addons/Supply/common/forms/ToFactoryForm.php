<?php

namespace addons\Supply\common\forms;
use addons\Supply\common\models\Produce;
use Yii;

use yii\base\Model;


/**
 * 布产-分配工厂 Form
 *
 * @property string $attr_require 必填属性
 * @property string $attr_custom 选填属性
 */
class ToFactoryForm extends Produce
{
    public $id;
    //属性必填字段
    public $supplier_id;
    //属性非必填
    public $follower_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
                [['supplier_id','follower_id'], 'required'],
           ];
        return array_merge(parent::rules() , $rules);
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        
        return  [
              'supplier_id'=>'供应商',
              'follower_id'=>'跟单人',
        ];
    }

    
}
