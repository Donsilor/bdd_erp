<?php

namespace addons\Warehouse\common\forms;

use common\models\forms\ImportForm;

/**
 * 货品盘点单数据导入 Form
 *
 */
class ImportBillWForm extends ImportForm
{
    public $file;
    public $bill_id;
    public $goods_id;
    public $goods_num;
    public $columns = [1=>'goods_id',2=>'goods_num'];
    public $requredColumns = ['goods_id','goods_num'];
    public $numberColumns = ['goods_num'];
    private $goodsIdsCache;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
                [['bill_id'], 'required'],
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
        return [
                'file'=>'上传文件',
                'bill_id'=>'bill_id',
        ];
    }
    
    /**
     * 校验 excel 行
     * @param array $row 行数据
     * @return boolean
     */
    public function loadRow($row,$rowIndex)
    {
        parent::loadRow($row, $rowIndex); 
        //验证重复货号
        if(!isset($this->goodsIdsCache[$this->goods_id])) {
            $this->goodsIdsCache[$this->goods_id] = true;
        }else {
            $this->addRowError($rowIndex, 'goods_id', "不可导入重复货号");
        }        
    }
}
