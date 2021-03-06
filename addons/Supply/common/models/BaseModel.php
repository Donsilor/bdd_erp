<?php

namespace addons\Supply\common\models;

use Yii;

/**
 * 款式库 基类Model
 * Class BaseModel
 * @package common\models\common
 * @author jianyan74 <751393839@qq.com>
 */
class BaseModel extends \common\models\base\BaseModel
{
    /**
     * 切换数据库
     * @return \yii\db\Connection
     */
    public static function getDb()
    {
         return \Yii::$app->db;
    }
    
    /**
     * 表前缀
     * @return string
     */
    public static function tablePrefix()
    {
        return "supply_";
    }
}