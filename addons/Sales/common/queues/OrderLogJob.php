<?php

namespace addons\Sales\common\queues;

use Yii;
use common\queues\RetryJob;

/**
 * 订单日志
 * Class OrderLogJob
 * @package common\queues
 * @author jianyan74 <751393839@qq.com>
 */
class OrderLogJob extends RetryJob
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
        Yii::$app->salesService->orderLog->realCreateOrderLog($this->data);
    }
}