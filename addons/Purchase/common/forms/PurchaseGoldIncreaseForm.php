<?php

namespace addons\Purchase\common\forms;

use Yii;
use addons\Supply\common\models\ProduceGold;

/**
 * 补石 Form
 *
 * @property string $increase_weight 必填属性 补石数量
 * @property string $increase_remark 选填属性
 */
class PurchaseGoldIncreaseForm extends ProduceGold
{
    
    
    public $increase_weight;
    public $increase_remark;
    
    public $attr;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
                [['increase_weight'], 'required'],
                [['increase_weight'], 'number'],
                [['increase_remark'], 'string','max'=>255],
        ];
        return array_merge(parent::rules() , $rules);
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        //合并
        return parent::attributeLabels() + [
                'increase_weight'=>'补料克重',
                'increase_remark'=>'补料备注',
        ];
    }
}