<?php

namespace common\components;

use Yii;
use common\enums\CacheEnum;
use common\enums\StatusEnum;
use addons\Shop\common\models\AttributeValue;
use addons\Shop\common\models\AttributeValueLang;
use addons\Shop\common\models\AttributeLang;
use addons\Shop\common\models\Attribute;


/**
 * Class Attribute
 * @package common\components
 * @author gaopeng
 */
class ShopAttribute
{
    /**
     * 返回属性名称
     *
     * @param string $name 字段名称
     * @param bool $noCache true 不从缓存读取 false 从缓存读取
     * @return bool|string
     */
    public function attrName($attr_id, $language = null,$noCache = false,$merchant_id = '')
    {
        if($language == null) {
            $language = \Yii::$app->params['language'];
        }
        $result = $this->getAttr($attr_id , $noCache);
        return $result['info'][$language]['name']??'';
    }
    /**
     * 返回ERP属性ID
     *
     * @param string $name 字段名称
     * @param bool $noCache true 不从缓存读取 false 从缓存读取
     * @return bool|string
     */
    public function erpAttrId($attr_id, $language = null, $noCache = false,$merchant_id = '')
    {
        if($language == null) {
            $language = \Yii::$app->params['language'];
        }
        $result = $this->getAttr($attr_id , $noCache);
        return $result['info'][$language]['erp_id']??'';
    }
    /**
     * 属性值列表
     * @param unknown $attr_id
     * @param unknown $language
     * @param string $noCache
     * @return array
     */
    public function valueList($attr_id, $language = null,$noCache = false,$merchant_id = '')
    {
        if($language == null) {
            $language = \Yii::$app->params['language'];
        }
        $result = $this->getAttr($attr_id , $noCache);
        return $result['items'][$language]??[];
    }   
    /**
     * 属性值键值对 映射
     * @param unknown $attr_id
     * @param unknown $language
     * @param string $noCache
     * @return array
     */
    public function valueMap($attr_id, $key = 'id', $value = "name", $language = null, $noCache = false)
    {
        $result = [];
        $data = $this->valueList($attr_id, $language, $noCache);
        if(!empty($data)){
            $result = array_column($data, $value,$key);
        }
        return $result;
    }
    /**
     * 返回ERP属性值ID
     * @param unknown $value_id
     * @param unknown $language
     * @param string $noCache
     * @return string
     */
    public function erpValueId($value_id, $language = null,$noCache = false , $merchant_id = '')
    {
        if($language == null) {
            $language = \Yii::$app->params['language'];
        }
        $result = $this->getAttrValue($value_id,$noCache);
        return $result[$language]['erp_id']??'';
    }
    /**
     * 返回属性值名称
     * @param unknown $value_id
     * @param unknown $language
     * @param string $noCache
     * @return string
     */
    public function valueName($value_id, $language = null,$noCache = false , $merchant_id = '')
    {
        if($language == null) {
            $language = \Yii::$app->params['language'];
        }
        $result = $this->getAttrValue($value_id,true);
        return $result[$language]['name']??'';
    }
    /**
     * 查询属性及其属性值
     * @param unknown $attr_id
     * @param string $noCache
     * @return array
     */
    public function getAttr($attr_id , $noCache = false , $merchant_id = '')
    {   $noCache = true;
        $cacheKey = CacheEnum::getPrefix('goodsAttr',$merchant_id).'-shop:'.$attr_id;
        if (!($info = Yii::$app->cache->get($cacheKey)) || $noCache == true) {
            $models = Attribute::find()->alias('attr')
                ->innerJoin(AttributeLang::tableName().' lang','attr.id=lang.master_id')
                ->select(['lang.master_id','lang.language','lang.attr_name','attr.erp_id'])
                ->where(['attr.id'=>$attr_id])
                ->asArray()->all();

            $info['info'] = [];
            foreach ($models as $row) {
                $info['info'][$row['language']] = [
                        'id'=>$row['master_id'],
                        'name'=>$row['attr_name'],
                        'erp_id'=>$row['erp_id'],
                ];
            }
            $models = AttributeValue::find()->alias("val")
                ->leftJoin(AttributeValueLang::tableName()." lang","val.id=lang.master_id")
                ->select(['lang.master_id',"val.code","val.erp_id","lang.attr_value_name",'lang.language'])
                ->where(['val.attr_id'=>$attr_id,'val.status'=>StatusEnum::ENABLED])
                ->orderBy('val.sort asc,val.id asc')
                ->asArray()->all();
            
            $value_list = [];
            foreach ($models as $row) {
                $value_list[$row['language']][] = [
                        'id'=>$row['master_id'],
                        'name'=>$row['attr_value_name'],
                        'code'=>$row['code'],
                        'erp_id'=>$row['erp_id']
                ];
            }
            $info['items'] = $value_list;
            
            $duration = (int) rand(3600*24,3600*24+3600);//防止缓存穿透
            // 设置缓存
            Yii::$app->cache->set($cacheKey, $info,$duration);
        }
        return $info;
    }
    /**
     * 查询属性值名称
     * @param unknown $value_id
     * @param string $noCache
     * @return array
     */
    public function getAttrValue($value_id , $noCache = false , $merchant_id = '')
    {   $noCache = true;
        $cacheKey = CacheEnum::getPrefix('goodsAttrValue',$merchant_id).'-shop:'.$value_id;
        if (!($info = Yii::$app->cache->get($cacheKey)) || $noCache == true) {            
            
            $models = AttributeValue::find()->alias("val")
                ->leftJoin(AttributeValueLang::tableName()." lang","val.id=lang.master_id")
                ->select(['lang.master_id',"lang.attr_value_name",'lang.language','val.erp_id'])
                ->where(['val.id'=>$value_id])
                ->asArray()->all();
            
            $info = [];
            foreach ($models as $row) {
                $info[$row['language']] = [
                        'id'=>$row['master_id'],
                        'name'=>$row['attr_value_name'],
                        'erp_id'=>$row['erp_id']
                ];
            }
            $duration = (int) rand(3600*24,3600*24+3600);//防止缓存穿透
            // 设置缓存
            Yii::$app->cache->set($cacheKey, $info,$duration);
        }
        return $info;
    }

}