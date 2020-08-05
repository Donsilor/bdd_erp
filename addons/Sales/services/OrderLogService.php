<?php

namespace addons\Sales\services;

use Yii;
use common\components\Service;
use addons\Sales\common\models\OrderLog;
use addons\Sales\common\queues\OrderLogJob;

/**
 * 订单日志
 * Class OrderLogService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class OrderLogService extends Service
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
     * 订单日志
     * @param array $log
     * @throws \Exception
     * @return int
     */
    public function createOrderLog($log)
    {
        if($this->switchQueue === true) {
            //队列
            $messageId = Yii::$app->queue->push(new OrderLogJob($log));
            return $messageId;
        }else {
            return $this->realCreateOrderLog($log);
        }      
    }
    /**
     * 创建日志
     * @param array $log
     * @throws \Exception
     * @return object
     */
    public function realCreateOrderLog($log)
    {
        $model = new OrderLog();
        $model->attributes = $log;        
        if(false === $model->save()){
            throw new \Exception($this->getError($model));
        }
        return $model;
    }
    
}