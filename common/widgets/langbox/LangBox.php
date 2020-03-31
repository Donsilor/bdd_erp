<?php

namespace common\widgets\langbox;

use yii\base\Widget;

/**
 * Class Provinces
 * @package common\widgets\provinces
 * @author jianyan74 <751393839@qq.com>
 */
class LangBox extends Widget
{
   /**
     * 模型
     *
     * @var array
     */
    public $model;

    /**
     * 表单
     * @var
     */
    public $form;
    public $fields;
    public $title;
    public $tab = 'tab';
    public $template = 'default';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        //初始化默认参数
        foreach ($this->fields as &$field){            
            $field['type'] = $field['type']??'textInput';
            $field['options'] = $field['options']??[];
            $field['label'] = $field['label']??null; 
        }
    }

    /**
     * @return string
     */
    public function run()
    {        
        return $this->render($this->template, [
            'form' => $this->form,
            'model' => $this->model,
            'title' => $this->title,   
            'tab' =>$this->tab,   
            'fields' => $this->fields
        ]);
    }
}

?>