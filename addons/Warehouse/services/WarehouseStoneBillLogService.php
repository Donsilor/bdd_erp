<?php

namespace addons\Warehouse\services;


use addons\Warehouse\common\models\WarehouseGoldLog;
use addons\Warehouse\common\models\WarehouseStoneBillLog;
use addons\Warehouse\common\queues\GoldLogJob;
use Yii;
use common\components\Service;


/**
 * 单据日志
 * Class WarehouseBillLogService
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class WarehouseStoneBillLogService extends Service
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
     * @return \addons\Warehouse\common\models\WarehouseGoldLog
     */
    public function createStoneBillLog($log)
    {
        if($this->switchQueue === true) {
            //队列
            $messageId = Yii::$app->queue->push(new StoneLogJob($log));
            return $messageId;
        }else {
            return $this->realCreateStoneBillLog($log);
        }      
    }
    /**
     * 创建日志
     * @param unknown $log
     * @throws \Exception
     * @return \addons\Warehouse\common\models\WarehouseGoldLog
     */
    public function realCreateStoneBillLog($log)
    {
        $model = new WarehouseStoneBillLog();
        $model->attributes = $log;
        if(false === $model->save()){
            throw new \Exception($this->getError($model));
        }
        return $model;
    }
    
}