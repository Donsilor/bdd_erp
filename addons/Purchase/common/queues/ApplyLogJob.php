<?php

namespace addons\Purchase\common\queues;

use Yii;
use common\queues\RetryJob;

/**
 * 采购申请单日志
 * Class ReceiptLogJob
 * @package common\queues
 * @author jianyan74 <751393839@qq.com>
 */
class ApplyLogJob extends RetryJob
{
    /**
     * 日志记录数据
     *
     * @var
     */
    public $data;
    
    /**
     * @param \yii\queue\Queue $queue
     * @return mixed|void
     * @throws \yii\base\InvalidConfigException
     */
    public function execute($queue)
    {
        Yii::$app->purchaseService->applyLog->realCreateApplyLog($this->data);
    }
}