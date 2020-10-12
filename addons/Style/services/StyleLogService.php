<?php

namespace addons\Style\services;

use Yii;
use common\components\Service;
use addons\Style\common\models\StyleLog;
use addons\Style\common\queues\StyleLogJob;

/**
 * 款式日志
 * Class StyleLogService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class StyleLogService extends Service
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
     * 款式日志
     * @param array $log
     * @throws \Exception
     * @return int
     */
    public function createStyleLog($log)
    {
        if($this->switchQueue === true) {
            //队列
            $messageId = Yii::$app->queue->push(new StyleLogJob($log));
            return $messageId;
        }else {
            return $this->realCreateStyleLog($log);
        }      
    }
    /**
     * 创建日志
     * @param array $log
     * @throws \Exception
     * @return object
     */
    public function realCreateStyleLog($log)
    {
        $model = new StyleLog();
        $model->attributes = $log;        
        if(false === $model->save()){
            throw new \Exception($this->getError($model));
        }
        return $model;
    }
    
}