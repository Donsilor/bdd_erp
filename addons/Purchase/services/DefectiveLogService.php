<?php

namespace addons\Purchase\services;

use addons\Purchase\common\queues\DefectiveLogJob;
use Yii;
use common\components\Service;
use addons\Purchase\common\models\PurchaseDefectiveLog;

/**
 * 收货单日志
 * Class DefectiveLogService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class DefectiveLogService extends Service
{
    public $switchQueue = false;
    /**
     * 队列开关
     * @param string $switchQueue
     * @return object
     */
    public function queue($switchQueue = false) {
        
        $this->switchQueue = $switchQueue;
        
        return $this;
    }
    /**
     * 收货单日志
     * @param array $log
     * @throws
     * @return int
     */
    public function createDefectiveLog($log)
    {
        if($this->switchQueue === true) {
            //队列
            $messageId = Yii::$app->queue->push(new DefectiveLogJob($log));
            return $messageId;
        }else {
            return $this->realCreateDefectiveLog($log);
        }
    }
    /**
     * 创建日志
     * @param array $log
     * @throws \Exception
     * @return object
     */
    public function realCreateDefectiveLog($log)
    {
        $model = new PurchaseDefectiveLog();
        $model->attributes = $log;
        $model->log_time = time();
        $model->creator_id = \Yii::$app->user->id;
        $model->creator = \Yii::$app->user->identity->username;
        if(false === $model->save()){
            throw new \Exception($this->getError($model));
        }
        return $model;
    }
    
}