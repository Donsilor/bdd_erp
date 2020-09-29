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
    public $uniqueKey ;
    public $uniqueColumn = [];
    public $columns = [];
    public $titles  = [];
    public $requredColumns = [];
    public $numberColumns = [];
    //文本属性
    public $attrInputColumns = [];
    //单选下拉属性
    public $attrSelectColumns = [];  
    private $_uniqueCache;
    private $_attrCache;
    private $_rowErrors;
    private $_error = false;
    
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
     * 检查字段是否需要重复校验
     * @return boolean true 需要校验   false不需要校验
     */
    public function isNeedCheck($attribute)
    {
        if($this->uniqueKey && in_array($attribute,$this->uniqueColumn) && !empty($this->_uniqueCache[$this->{$this->uniqueKey}])) {
             return false;
        }
        return true;
    }
    /**
     * 校验excel 行
     * @param unknown $row
     * @param unknown $rowIndex
     * @return boolean
     */
    public function loadRow($row,$rowIndex)
    {    
        foreach ($row as $attribute=> $colValue) {
            
            $this->{$attribute} = trim(preg_replace("/\[|\]|\【|\】/is",'',$colValue));
            
            if($this->isNeedCheck($attribute)) {
                //必填校验
                if(in_array($attribute,$this->requredColumns) && $colValue === '') {
                    $this->addRowError($rowIndex, $attribute, "不能为空");
                }
                //数字校验
                if(in_array($attribute,$this->numberColumns) && $colValue != '') {
                    if(is_numeric($colValue) && $colValue >= 0) {
                        $row[$attribute] = $colValue;
                    }else {
                        $this->addRowError($rowIndex, $attribute, "[{$colValue}]必须为数字");
                    }
                }
                if($this->uniqueKey) {
                    $this->_uniqueCache[$this->{$this->uniqueKey}] = true;
                }                
            }
        }
        //属性验证处理
         foreach ($this->attrInputColumns ?? [] as $attr_id =>$attr_code) {
            $attr_value =  $this->{$attr_code};
            if($attr_value) {
                $this->{$attr_code} =  ['attr_id'=>$attr_id,'attr_value_id'=>0,'attr_value'=>$this->{$attr_code}];
            }
        }
        foreach ($this->attrSelectColumns ?? [] as $attr_id =>$attr_code) {
            $attr_value =  $this->{$attr_code};            
            if($attr_value) {
                if(!empty($this->_attrCache[$attr_id])) {
                    $map = $this->_attrCache[$attr_id];
                }else {
                    $map = \Yii::$app->attr->valueMap($attr_id,'name','id');
                    $this->_attrCache[$attr_id] = $map;
                }
                if(empty($map) || empty($map[$attr_value])) {
                    $attr_value_id = 0;
                    $this->addRowError($rowIndex, $attr_code, "[".$attr_value."]不存在");
                }else{
                    $attr_value_id = $map[$attr_value];
                } 
                $this->{$attr_code} = ['attr_id'=>$attr_id,'attr_value_id'=>$attr_value_id,'attr_value'=>$attr_value];
            }
        }  

        return true;
    }
    /**
     * 添加错误
     * {@inheritDoc}
     * @see \yii\base\Model::addError()
     */
    public function addRowError($rowIndex,$attribute,$error)
    {
        $this->_error = true;
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
     * 是否出错
     * @return boolean
     */
    public function hasError()
    {
        return $this->_error;
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
                        $message .= "【".($this->titles[$code]??$code)."=>".($error[$code]??'')."】";
                    }
                }
                if(isset($error['error'])) {
                    $message .= "【".$error['error']."】";
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
