<?php

namespace addons\Purchase\common\forms;

use Yii;
use addons\Supply\common\models\ProduceStone;

/**
 * 补石 Form
 *
 * @property string $increase_num 必填属性 补石数量
 * @property string $increase_remark 选填属性
 */
class PurchaseStoneIncreaseForm extends ProduceStone
{    
    

    public $increase_num;
    public $increase_remark;
    
    public $attr;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
                [['increase_num'], 'required'],
                [['increase_num'], 'number'],
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
                'increase_num'=>'补石数量',
                'increase_remark'=>'补石备注',
        ];
    }
}