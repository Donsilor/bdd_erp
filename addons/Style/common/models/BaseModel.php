<?php

namespace addons\style\common\models;

use Yii;

/**
 * Class BaseModel
 * @package common\models\common
 * @author jianyan74 <751393839@qq.com>
 */
class BaseModel extends \common\models\base\BaseModel
{
    
    public static function getDb() 
    {
         return \Yii::$app->styleDb;    
    }
    
    /**
     * 获取数据库名称
     * @return unknown
     */
    public static function dbName()
    {
        return parent::dbName();
    }
     
}