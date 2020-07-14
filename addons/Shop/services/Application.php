<?php

namespace addons\Shop\services;

use common\components\Service;

/**
 * Class Application
 *
 * @package addons\Shop\services
 * @property \addons\Shop\services\OrderService $order 订单
 */
class Application extends Service
{
    /**
     * @var array
     */
    public $childService = [
            'order' => 'addons\Shop\services\OrderService',            
    ];
}