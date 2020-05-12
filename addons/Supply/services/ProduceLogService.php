<?php
/**
 * Created by PhpStorm.
 * User: BDD
 * Date: 2019/12/7
 * Time: 13:53
 */

namespace addons\Supply\services;


use addons\Supply\common\models\ProduceAttribute;
use addons\Supply\common\models\ProduceLog;
use common\components\Service;
use common\helpers\SnHelper;
use common\models\common\ReportLog;


class ProduceLogService extends Service
{


    /**
     * 创建布产日志
     * @return array
     */
    public function createProduceLog($log){
        
        $produce_log = new ProduceLog();
        $produce_log->attributes = $log;
        $produce_log->log_time = time();
        $produce_log->creator_id = \Yii::$app->user->id;
        $produce_log->creator = \Yii::$app->user->identity->username;
        if(false === $produce_log->save()){
            throw new \Exception($this->getError($produce_log));
        }
        return $produce_log ;
    }

    


}