<?php

namespace common\components;

use Yii;
use common\enums\CacheEnum;
use common\models\common\Country;


/**
 * Class Country
 * @package common\components
 * @author gaopeng
 */
class Area
{
    /**
     * 国家/省份/城市名称
     *
     * @param string $name 字段名称
     * @param bool $noCache true 不从缓存读取 false 从缓存读取
     * @return bool|string
     */
    public function name($id, $language = null, $noCache = false, $merchant_id = '')
    {
        if($language == null) {
            $language = \Yii::$app->params['language'];
        }        
        $language = strtolower(str_replace("-", '_', $language));        
        $model = $this->getModel($id , $noCache, $merchant_id);
        return $model['name_'.$language] ?? '';
    }
    /**
     * 查询国家省份城市单行信息
     * @param int $id
     * @param string $noCache
     * @return array
     */
    public function getModel($id , $noCache = false , $merchant_id = '')
    {
        $cacheKey = CacheEnum::getPrefix('countryRow',$merchant_id).':'.$id;
        if (!($model = Yii::$app->cache->get($cacheKey)) || $noCache == true) {
            $model = Country::find()->where(['id'=>$id])->asArray()->one();
            if($model) {            
                $duration = (int) rand(3600*24,3600*24+3600);//防止缓存穿透
                // 设置缓存
                Yii::$app->cache->set($cacheKey, $model,$duration);
            }
        }
        return $model ?? [];
    }
    
    
}