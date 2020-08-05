<?php

namespace common\queues;

use Yii;
use yii\base\BaseObject;

/**
 * 
 *  基类 job
 * Class Job
 * @package common\queues
 * @author jianyan74 <751393839@qq.com>
 */
class RetryJob extends BaseObject implements \yii\queue\RetryableJobInterface
{
    public $ttr = 60;
    public $attempts = 3;
    
    public function execute($queue) {
        
    }
    
    public function canRetry($attempt, $error)
    {
        return $attempt < $this->attempts;
    }
    
    public function getTtr()
    {
        return $this->ttr;
    }
}