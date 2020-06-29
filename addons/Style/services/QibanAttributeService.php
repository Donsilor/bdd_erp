<?php

namespace addons\Style\services;

use addons\Style\common\models\Qiban;
use addons\Style\common\models\QibanAttribute;
use Yii;
use common\components\Service;
use addons\Style\common\models\Style;
use addons\Style\common\models\StyleAttribute;
use addons\Style\common\models\AttributeSpec;
use common\enums\StatusEnum;
use addons\Style\common\enums\AttrTypeEnum;


/**
 * Class TypeService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class QibanAttributeService extends Service
{
    
    
    /**
     * 创建 款式属性关联
     * @param unknown $qiban_id
     * @param array $attr_list
     */
    public function createQibanAttribute($qiban_id,array $attr_list,$attr_type = null)
    {
        $qiban = Qiban::find()->select(['id','style_cate_id'])->where(['id'=>$qiban_id])->one();
        
        //批量删除
        $updateWhere = ['qiban_id'=>$qiban_id];
        if($attr_type) {
            $updateWhere['attr_type'] = $attr_type;
        }else {
            $updateWhere['attr_type'] = [1,3,4];
        }
        QibanAttribute::updateAll(['status'=>StatusEnum::DELETE],$updateWhere);
        foreach ($attr_list as $attr_id => $attr_value) {
            $spec = AttributeSpec::find()->where(['attr_id'=>$attr_id,'style_cate_id'=>$qiban->style_cate_id])->one();
            $model = QibanAttribute::find()->where(['qiban_id'=>$qiban_id,'attr_id'=>$attr_id])->one();
            if(!$model) {
                $model = new QibanAttribute();
                $model->qiban_id = $qiban_id;
                $model->attr_id  = $attr_id;
            }
            $model->is_require = $spec->is_require;
            $model->input_type = $spec->input_type;
            $model->attr_type = $spec->attr_type;
            $model->attr_values = is_array($attr_value) ? implode(',',$attr_value) : $attr_value;
            $model->status = StatusEnum::ENABLED;
            $model->save();
        }
    }
    
    /**
     * 获取起版属性列表
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getQibanAttrList($qiban_id,$attr_type)
    {
        return QibanAttribute::find()->where(['qiban_id'=>$qiban_id,'attr_type'=>$attr_type])->asArray()->all();
    }
    
    /**
     * 起版属性值下拉列表
     * @param unknown $style_id
     * @param unknown $attr_id
     * @return array|array
     */
    public function getDropdowns($qiban_id,$attr_id)
    {
        $model = QibanAttribute::find()->select(['attr_values'])->where(['qiban_id'=>$qiban_id,'attr_id'=>$attr_id])->one();
        if(empty($model) || !$model->attr_values){
            return [];
        }
        return Yii::$app->styleService->attribute->getValuesByValueIds($model->attr_values);
    }
    
}