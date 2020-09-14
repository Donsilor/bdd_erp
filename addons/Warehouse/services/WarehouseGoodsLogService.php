<?php

namespace addons\Warehouse\services;


use addons\Warehouse\common\models\WarehouseGoodsLog;
use addons\Warehouse\common\queues\GoodsLogJob;
use Yii;
use common\components\Service;


/**
 * 单据日志
 * Class WarehouseBillLogService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseGoodsLogService extends Service
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
    public function createGoodsLog($log)
    {
        if($this->switchQueue === true) {
            //队列
            $messageId = Yii::$app->queue->push(new GoodsLogJob($log));
            return $messageId;
        }else {
            return $this->realCreateGoodsLog($log);
        }      
    }
    /**
     * 创建日志
     * @param unknown $log
     * @throws \Exception
     * @return \addons\Warehouse\common\models\WarehouseBillLog
     */
    public function realCreateGoodsLog($log)
    {
        $model = new WarehouseGoodsLog();
        $model->attributes = $log;
        if(false === $model->save()){
            throw new \Exception($this->getError($model));
        }
        return $model;
    }


}