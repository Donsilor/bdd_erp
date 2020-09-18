<?php

namespace addons\Sales\common\forms;

use Yii;
use yii\base\Model;

/**
 * 订单 Form
 */
class OrderImportForm extends Model
{
    
       
    public $sale_channel_id;
    public $file;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
                [['sale_channel_id'],'required'],
                [['file'], 'required','isEmpty'=>function($value){
                    return !empty($this->file);
                }],
                [['file'], 'file', 'extensions' => ['xlsx']],
            ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        //合并
        return [
                'sale_channel_id'=>'销售渠道',
                'file'=>'上传文件',                
        ];
    }  
    
}
