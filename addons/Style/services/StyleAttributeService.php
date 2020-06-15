<?php

namespace addons\Style\services;

use addons\Style\common\models\AttributeIndex;
use common\helpers\ArrayHelper;
use Yii;
use common\components\Service;
use addons\Style\common\models\Style;
use addons\Style\common\models\StyleAttribute;
use addons\Style\common\models\AttributeSpec;
use common\enums\StatusEnum;
use addons\Style\common\enums\AttrTypeEnum;
use addons\Style\common\enums\JintuoTypeEnum;


/**
 * Class TypeService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class StyleAttributeService extends Service
{
    /**
     * 获取款式属性列表
     * @param unknown $style_id
     * @param unknown $jintuo_type
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getStyleAttrList($style_id , $attr_type = [])
    {
        return StyleAttribute::find()->where(['style_id'=>$style_id,'attr_type'=>$attr_type])->orderBy('sort asc')->asArray()->all();
    }


    /**
     * 根据款获取属性选项值
     */
    public function getAttrValueListByStyle($style_sn,$attr_id){
        $attr_arr = Style::find()->alias('style')
            ->innerJoin(StyleAttribute::tableName().' style_attr','style_attr.style_id=style.id')
            ->where(['style.style_sn'=>$style_sn,'style_attr.attr_id'=>$attr_id])
            ->select(['style_attr.attr_values'])
            ->asArray()
            ->one();
        if(empty($attr_arr) || $attr_arr['attr_values'] ==''){
            return [];
        }
        $attr_values = $attr_arr['attr_values'];
        return Yii::$app->styleService->attribute->getValuesByValueIds($attr_values);

    }
    
}