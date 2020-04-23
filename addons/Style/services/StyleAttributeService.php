<?php

namespace addons\Style\services;

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
class StyleAttributeService extends Service
{
    
    
    /**
     * 创建 款式属性关联
     * @param unknown $style_id
     * @param array $attr_list
     */
    public function createStyleAttribute($style_id,array $attr_list,$attr_type = null)
    {
        $style = Style::find()->select(['id','style_cate_id'])->where(['id'=>$style_id])->one();
        
        //批量删除
        $updateWhere = ['style_id'=>$style_id];
        if($attr_type) {
            $updateWhere['attr_type'] = $attr_type;
        }else {
            $updateWhere['attr_type'] = [1,3,4];
        }
        StyleAttribute::updateAll(['status'=>StatusEnum::DELETE],$updateWhere);
        foreach ($attr_list as $attr_id => $attr_value) {
            $spec = AttributeSpec::find()->where(['attr_id'=>$attr_id,'style_cate_id'=>$style->style_cate_id])->one();
            $model = StyleAttribute::find()->where(['style_id'=>$style_id,'attr_id'=>$attr_id])->one();
            if(!$model) {
                $model = new StyleAttribute();
                $model->style_id = $style_id;
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


    
}