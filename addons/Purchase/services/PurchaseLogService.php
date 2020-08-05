<?php

namespace addons\Purchase\services;

/**
 * 收货单日志
 * Class ReceiptLogService
*/

use addons\Purchase\common\models\PurchaseGiftLog;
use Yii;
use common\components\Service;
use addons\Purchase\common\models\PurchaseReceiptLog;
use addons\Purchase\common\models\PurchaseLog;
use addons\Purchase\common\queues\PurchaseLogJob;
use addons\Purchase\common\enums\PurchaseTypeEnum;
use addons\Purchase\common\models\PurchasePartsLog;
use addons\Purchase\common\models\PurchaseGoldLog;
use addons\Purchase\common\models\PurchaseStoneLog;

/**
 * 采购单日志
 * Class PurchaseLogService
>>>>>>> 33f97b689776b9b3f8fb51c70a1e085041d21a7f
 * @package services\common
 * @author jianyan74 <751393839@qq.com>
 */
class PurchaseLogService extends Service
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
     * 采购单日志
     * @param array $log
     * @throws
     * @return int
     */

    public function createPurchaseLog($log,$purchase_type = null)
    {
        if($this->switchQueue === true) {
            //队列
            $messageId = Yii::$app->queue->push(new PurchaseLogJob($log,$purchase_type));
            //return $messageId;
        }else {
            return $this->realCreatePurchaseLog($log,$purchase_type);
        }
    }
    /**
     * 创建日志
     * @param array $log
     * @throws \Exception
     * @return object
     */

    public function realCreatePurchaseLog($log, $purchase_type = null)
    {
        switch ($purchase_type) {
            case  PurchaseTypeEnum::MATERIAL_GOLD :{
                $model = new PurchaseGoldLog();
                break;
            }
            case  PurchaseTypeEnum::MATERIAL_STONE :{
                $model = new PurchaseStoneLog();
                break;
            }
            case  PurchaseTypeEnum::MATERIAL_PARTS :{
                $model = new PurchasePartsLog();
                break;
            }
            case  PurchaseTypeEnum::MATERIAL_GIFT :{
                $model = new PurchaseGiftLog();
                break;
            }
            default:{
                $purchase_type = PurchaseTypeEnum::GOODS;
                $model = new PurchaseLog();
                break;
            }
        }
        $model->attributes = $log;
        $model->log_time = time();
        $model->creator_id = \Yii::$app->user->id ?? 0;
        $model->creator = \Yii::$app->user->identity->username ?? '';
        if(false === $model->save()){
            throw new \Exception($this->getError($model));
        }
        return $model;
    }
    
}