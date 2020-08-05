<?php

namespace addons\Warehouse\services;


use Yii;
use common\components\Service;
use addons\Warehouse\common\models\WarehouseBillLog;
use addons\Warehouse\common\queues\BillLogJob;


/**
 * 单据日志
 * Class WarehouseBillLogService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseBillLogService extends Service
{
    public $switchQueue = false;
    /**
     * 队列开关
     * @param string $switchQueue
     * @return \addons\Warehouse\services\WarehouseBillLogService
     */
    public function queue($switchQueue = false) {
        
        $this->switchQueue = $switchQueue;
        
        return $this;
    }
    /**
     * 单据日志
     * @param array $log
     * @throws \Exception
     * @return \addons\Warehouse\common\models\WarehouseBillLog
     */
    public function createBillLog($log)
    {
        if($this->switchQueue === true) {
            //队列
            $messageId = Yii::$app->queue->push(new BillLogJob($log));            
            return $messageId;
        }else {
            return $this->realCreateBillLog($log);
        }      
    }
    /**
     * 创建日志
     * @param unknown $log
     * @throws \Exception
     * @return \addons\Warehouse\common\models\WarehouseBillLog
     */
    public function realCreateBillLog($log) 
    {
        $model = new WarehouseBillLog();
        $model->attributes = $log;
        if(false === $model->save()){
            throw new \Exception($this->getError($model));
        }
        return $model;
    }
    
}