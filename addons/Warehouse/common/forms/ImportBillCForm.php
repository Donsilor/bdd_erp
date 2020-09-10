<?php

namespace addons\Warehouse\common\forms;

use addons\Warehouse\common\models\WarehouseBill;
use common\helpers\ArrayHelper;

/**
 * 其它出库单导入 Form
 *
 */
class ImportBillCForm extends WarehouseBill
{
    public $file;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
                [['delivery_type'], 'required'],
                [['file'], 'required','isEmpty'=>function($value){
                    return !empty($this->file);
                }],
                [['file'], 'file', 'extensions' => ['xlsx']],//'skipOnEmpty' => false,
        ];
        return array_merge(parent::rules() , $rules);
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        //合并
        return ArrayHelper::merge(parent::attributeLabels() , [
                'file'=>'上传文件',
                'channel_id'=>'出库渠道',
        ]);
    }    
}
