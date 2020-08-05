<?php

namespace addons\Supply\common\forms;

use addons\Supply\common\enums\GoodsTypeEnum;
use common\enums\TargetTypeEnum;
use common\helpers\ArrayHelper;
use Yii;

use addons\Supply\common\models\Supplier;
/**
 * 供应商 Form
 */
class SupplierForm extends Supplier
{
    //审批流程
    public $targetType;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['supplier_code','supplier_name','supplier_tag'], 'unique'],
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
            //'id'=>'序号',
        ]);
    }

    public function getTargetType(){
        switch ($this->goods_type){
            case GoodsTypeEnum::COMMODITY:
                $this->targetType = TargetTypeEnum::SUPPLIER_GOODS_MENT;
                break;
            case GoodsTypeEnum::RAW_MATERIAL:
                $this->targetType = TargetTypeEnum::SUPPLIER_MATERIAL_MENT;
                break;
            default:
                $this->targetType = false;
        }
    }

}
