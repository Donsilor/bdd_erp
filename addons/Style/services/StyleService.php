<?php

namespace addons\Style\services;

use Yii;
use common\components\Service;
use addons\Style\common\models\Style;
use common\helpers\Url;
use addons\Style\common\models\AttributeSpec;
use addons\Style\common\models\StyleAttribute;
use common\enums\StatusEnum;


/**
 * Class TypeService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class StyleService extends Service
{
    
    /**
     * 款式编辑 tab
     * @param int $id 款式ID
     * @return array
     */
    public function editTabList($id)
    {
        $tab_list = [
                1=>['name'=>'基础信息','url'=>Url::to(['edit-info','id'=>$id,'tab'=>1])],
                2=>['name'=>'款式属性','url'=>Url::to(['edit-attr','id'=>$id,'tab'=>2])],
                3=>['name'=>'款式规格','url'=>Url::to(['edit-goods','id'=>$id,'tab'=>3])],
                4=>['name'=>'石头信息','url'=>Url::to(['edit-stone','id'=>$id,'tab'=>4])],
                5=>['name'=>'工厂信息','url'=>Url::to(['edit-factory','id'=>$id,'tab'=>5])],
                6=>['name'=>'工费信息','url'=>Url::to(['edit-factory-fee','id'=>$id,'tab'=>6])],
                7=>['name'=>'款式图片','url'=>Url::to(['edit-images','id'=>$id,'tab'=>7])],
                8=>['name'=>'日志信息','url'=>Url::to(['logs','id'=>$id,'tab'=>8])]
        ];
        
        return $tab_list;
    }
    /**
     * 创建 款式属性关联
     * @param unknown $style_id
     * @param array $attr_list
     */
    public function createStyleAttribute($style_id,array $attr_list)
    {   
        $style = Style::find()->select(['id','style_cate_id'])->where(['id'=>$style_id])->one();
        //批量删除
        StyleAttribute::updateAll(['status'=>StatusEnum::DISABLED],['style_id'=>$style_id]);
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