<?php

namespace addons\Supply\common\forms;

use common\helpers\ArrayHelper;
use Yii;

use addons\Supply\common\models\Supplier;
/**
 * 供应商 Form
 */
class SupplierForm extends Supplier
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['supplier_code','supplier_name'], 'unique'],
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
            'id'=>'序号',
        ]);
    }

}
