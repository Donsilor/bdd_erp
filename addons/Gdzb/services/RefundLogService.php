<?php

namespace addons\Gdzb\services;

use addons\Gdzb\common\models\RefundLog;
use Yii;
use common\components\Service;

/**
 * 订单日志
 * Class OrderLogService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class RefundLogService extends Service
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
    public function createRefundLog($log)
    {
        if($this->switchQueue === true) {
            //队列
            $messageId = Yii::$app->queue->push(new OrderLogJob($log));
            return $messageId;
        }else {
            return $this->realCreateRefundLog($log);
        }      
    }
    /**
     * 创建日志
     * @param array $log
     * @throws \Exception
     * @return object
     */
    public function realCreateRefundLog($log)
    {
        $model = new RefundLog();
        $model->attributes = $log;        
        if(false === $model->save()){
            throw new \Exception($this->getError($model));
        }
        return $model;
    }
    
}