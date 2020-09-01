<?php

namespace addons\Gdzb\services;

use common\components\Service;

/**
 * Class Application
 *
 * @package addons\Gdzb\services
 * @property \addons\Gdzb\services\OrderService $order 订单
 * @var array
 */
class Application extends Service
{
    
    public $childService = [
            /*********订单相关*********/
            'order' => 'addons\Gdzb\services\OrderService',            
    ];
}