<?php

namespace common\models\forms;

use Yii;
use yii\base\Model;

/**
 * 导入 Form
 */
class ImportForm extends Model
{
    public $file;    
    public $columns = [];
    public $titles  = [];
    
    private $_rowErrors;
     /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
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
                'file'=>'上传文件',                
        ];
    }
    /**
     * 校验excel 行
     * @param unknown $row
     * @param unknown $rowIndex
     * @return boolean
     */
    public function loadRow($row,$rowIndex)
    {
        return true;
    }
    /**
     * 添加错误
     * {@inheritDoc}
     * @see \yii\base\Model::addError()
     */
    public function addRowError($rowIndex,$attribute,$error)
    {
        $this->_rowErrors[$rowIndex][$attribute] = $error;
    }    
    /**
     * 查询错误
     * @return unknown
     */
    public function getRowErrors()
    {
        return $this->_rowErrors;
    }
    /**
     * 下载错误提示
     * @return string
     */
    public function showImportMessage()
    {
        $message = '';
        if ($this->_rowErrors) {
            //发生错误
            foreach ($this->_rowErrors as $k => $error) {
                $message .= '第' . ($k) . '行：';
                foreach ($this->columns as $code) {
                    if(isset($error[$code])) {
                        $message .= "【".$this->titles[$code]."=>".$error[$code]."】";
                    }
                }
                $message .= PHP_EOL;
            }
            header("Content-Disposition: attachment;filename=错误提示" . date('YmdHis') . ".log");
            echo iconv("utf-8", "gbk", $message);
            exit();
        }
        return $message;
    }
}
