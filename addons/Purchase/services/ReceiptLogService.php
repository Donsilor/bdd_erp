<?php

namespace addons\Purchase\services;

use addons\Purchase\common\queues\ReceiptLogJob;
use Yii;
use common\components\Service;
use addons\Purchase\common\models\PurchaseReceiptLog;

/**
 * 收货单日志
 * Class ReceiptLogService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class ReceiptLogService extends Service
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
    public function createReceiptLog($log)
    {
        if($this->switchQueue === true) {
            //队列
            $messageId = Yii::$app->queue->push(new ReceiptLogJob($log));
            return $messageId;
        }else {
            return $this->realCreateReceiptLog($log);
        }      
    }
    /**
     * 创建日志
     * @param array $log
     * @throws \Exception
     * @return object
     */
    public function realCreateReceiptLog($log)
    {        
        $model = new PurchaseReceiptLog();
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