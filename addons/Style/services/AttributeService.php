<?php

namespace addons\Style\services;

use Yii;
use common\components\Service;
use common\helpers\ArrayHelper;
use addons\Style\common\models\AttributeValueLang;
use addons\Style\common\models\Attribute;
use addons\Style\common\models\AttributeLang;
use addons\Style\common\models\AttributeValue;
use addons\Style\common\models\AttributeSpec;
use addons\Style\common\enums\InlayEnum;
use addons\Style\common\enums\AttrModuleEnum;


/**
 * Class AttributeService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class AttributeService extends Service
{
    public $module = null;
    
    /**
     * 更新属性值
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function updateAttrValues($attr_id)
    {
        $sql1 = 'UPDATE '.AttributeLang::tableName().' set attr_values=null where master_id = '.$attr_id;
        
        $sql2 = 'UPDATE '.AttributeLang::tableName().' attr_lang,
             (
            	SELECT
            		val.attr_id,
            		val_lang.`language`,
            		GROUP_CONCAT(attr_value_name order by sort asc) AS attr_values
            	FROM
            		'.AttributeValueLang::tableName().' val_lang
            	INNER JOIN '.AttributeValue::tableName().' val ON val_lang.master_id = val.id
            	WHERE
            		val.attr_id = '.$attr_id.' and val.`status`=1 
            	GROUP BY
            		val.attr_id,
            		val_lang.`language`
            ) t
            SET attr_lang.attr_values = t.attr_values
            WHERE
            	attr_lang.master_id = t.attr_id
            AND attr_lang.`language` = t.`language`
            AND attr_lang.master_id = '.$attr_id.';';
        $res1 = \Yii::$app->db->createCommand($sql1)->execute();
        $res2 = \Yii::$app->db->createCommand($sql2)->execute();
        return $res1 && $res2;
    }
    /**
     * 基础属性下拉列表
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getDropDown($status = 1,$usage = 1,$language = null)
    {
        if(empty($language)){
            $language = Yii::$app->params['language'];
        }
        
        $query = Attribute::find()->alias('a')
            ->leftJoin(AttributeLang::tableName().' b', 'b.master_id = a.id and b.language = "'.$language.'"')
            ->select(['a.*',"if((b.remark='' or b.remark is null),b.attr_name,concat(b.attr_name,'(',b.remark,')')) as attr_name"]);
  
        if( $status !== null){
            $query->andWhere(['=','a.status',$status]);
        }
        
        $query ->orderBy('sort asc,created_at asc');
        $models = $query->asArray()->all();
        
        return ArrayHelper::map($models,'id','attr_name');
    }


    /**
     * 根据属性ID查询属性名称
     * @param unknown $attr_id
     * @param unknown $language
     */
    public function getAttrNameByAttrId($attr_id,$language = null)
    {
        if(empty($language)){
            $language = Yii::$app->params['language'];
        }
        $model = Attribute::find()->alias("val")
            ->leftJoin(AttributeLang::tableName()." lang","val.id=lang.master_id and lang.language='".$language."'")
            ->select(['val.id',"lang.attr_name"])
            ->where(['val.id'=>$attr_id])
            ->one();
        return $model ? $model->attr_name : '';
    }
    /**
     * 初始化属性模块
     * @param unknown $modules
     * @return AttributeService
     */
    public function module($module = null)
    {
        $this->module = $module;           
        return $this; 
    }

    /**
     * 查询款式属性列表（不按照属性类型分组）
     * @param unknown $style_cate_id
     * @param unknown $attr_type
     * @param unknown $is_inlay 是否镶嵌
     * @param number $status
     * @param unknown $language
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getAttrListByCateId($style_cate_id, $attr_type = null,$is_inlay = null, $status = 1, $language = null)
    {
        if(empty($language)){
            $language = Yii::$app->params['language'];
        }
        $query = AttributeSpec::find()->alias("spec")
            ->select(["attr.id","lang.attr_name","attr.code",'spec.attr_type','spec.input_type','spec.is_require','spec.modules'])
            ->innerJoin(Attribute::tableName()." attr",'spec.attr_id=attr.id')
            ->innerJoin(AttributeLang::tableName().' lang',"attr.id=lang.master_id and lang.language='".$language."'")
            ->where(['spec.style_cate_id'=>$style_cate_id]);
        if(is_numeric($status)){
            $query->andWhere(['=','spec.status',$status]);
        }
        if(!empty($attr_type)) {
            $query->andWhere(['spec.attr_type'=>$attr_type]);
        }
        if($is_inlay !== null && $is_inlay == InlayEnum::No) {
            $query->andWhere(['spec.is_inlay'=>$is_inlay]);
        }        
        $_models = $query->orderBy("spec.sort asc")->asArray()->all();
        
        $models = [];
        foreach ($_models as $model) {
            $modules = !empty($model['modules']) ? explode(',', $model['modules']) :[]; 
            if(!$this->module || in_array($this->module,$modules)){
                $models[] = $model;
            }            
        }
        return $models;
    }
    /**
     * 根据款式分类查询 属性列表  按照属性类型分组
     * @param int $style_cate_id
     * @param int or array(int) $attr_type
     * @param number $status
     * @param string $language
     * @return array
     */
    public function getAttrTypeListByCateId($style_cate_id, $attr_type = null, $is_inlay = null,$status = 1, $language = null)
    {
        $models = $this->getAttrListByCateId($style_cate_id, $attr_type,$is_inlay ,$status, $language);
        $attr_list = [];
        foreach ($models as $model){
            $attr_list[$model['attr_type']][] = $model;
        }
        ksort($attr_list);
        return $attr_list;
    }
    /**
     * 根据属性ID和产品线ID查询 属性列表
     * @param unknown $attr_ids
     * @param unknown $style_cate_id
     * @param number $status
     * @param unknown $language
     */
    public function getSpecAttrList($attr_ids,$style_cate_id,$status = 1,$language = null)
    {
        if(empty($language)){
            $language = Yii::$app->params['language'];
        }
        $query = AttributeSpec::find()->alias("spec")
            ->select(["attr.id","lang.attr_name",'spec.attr_type','spec.input_type','spec.is_require'])
            ->innerJoin(Attribute::tableName()." attr",'spec.attr_id=attr.id')
            ->innerJoin(AttributeLang::tableName().' lang',"attr.id=lang.master_id and lang.language='".$language."'")
            ->where(['spec.style_cate_id'=>$style_cate_id,'spec.attr_id'=>$attr_ids]);
        if(is_numeric($status)){
            $query->andWhere(['=','spec.status',$status]);
        }
        $models = $query->orderBy("spec.sort asc,spec.id asc")->asArray()->all();

        return $models;
    }
    
    /**
     * 根据属性ID查询属性值列表
     * @param unknown $attr_id
     * @param unknown $language
     */
    public function getValuesByAttrId($attr_id,$status = 1,$language = null)
    {
        if(empty($language)){
            $language = Yii::$app->params['language'];
        }
        $query = AttributeValue::find()->alias("val")
                    ->leftJoin(AttributeValueLang::tableName()." lang","val.id=lang.master_id and lang.language='".$language."'")
                    ->select(['val.id',"lang.attr_value_name"])
                    ->where(['val.attr_id'=>$attr_id]);        
        if(is_numeric($status)){
            $query->andWhere(['=','val.status',$status]);
        }
        $models = $query->orderBy('val.sort asc,val.id asc')->asArray()->all();
        
        return array_column($models,'attr_value_name','id');
    }
    /**
     * 根据属性值ID，查询属性值列表
     * @param unknown $ids
     * @param unknown $language
     * @return array
     */
    public function getValuesByValueIds($ids, $language = null)
    {
        if(!is_array($ids)){
            $ids = explode(",",$ids);
        }
        if(empty($language)){
            $language = Yii::$app->params['language'];
        }        
        $query = AttributeValue::find()->alias("val")
                ->leftJoin(AttributeValueLang::tableName()." lang","val.id=lang.master_id and lang.language='".$language."'")
                ->select(['val.id',"lang.attr_value_name"])
                ->where(['val.id'=>$ids]);
        
        $models = $query->orderBy('val.sort asc')->asArray()->all();        
        return array_column($models,'attr_value_name','id');
    }
    /**
     * 根据属性值ID，查询属性值列表
     * @param unknown $ids
     * @param unknown $language
     * @return array
     */
    public function getAttributeByValueIds($ids,$language = null)
    {
        if(empty($language)){
            $language = Yii::$app->params['language'];
        }
        if(!is_array($ids)){
            $ids = explode(",",$ids);
        }
        $models = AttributeValue::find()->alias("val")
            ->leftJoin(AttributeValueLang::tableName()." lang","val.id=lang.master_id and lang.language='".$language."'")
            ->select(['val.attr_id','lang.attr_value_name'])
            ->where(['in','val.id',$ids])
            ->asArray()->all();
        return array_column($models, 'attr_value_name','attr_id');
    }
    /**
     * 根据属性值ID，查询属性值列表
     * @param unknown $ids
     * @param unknown $language
     * @return array
     */
    public function getAttrValuesByValueIds($ids,$language = null)
    {
        if(empty($language)){
            $language = Yii::$app->params['language'];
        }
        if(!is_array($ids)){
            $ids = explode(",",$ids);
        }
        $models = AttributeValue::find()->alias('val')
            ->leftJoin(AttributeValueLang::tableName()." lang","val.id=lang.master_id and lang.language='".$language."'")
            ->select(['val.id','attr_value_name as name','image as img'])
            ->where(['in','val.id',$ids])
            ->asArray()->all();
        return $models;
    }

}