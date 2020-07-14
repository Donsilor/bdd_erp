<?php

namespace addons\Shop\services;

use common\components\Service;

/**
 * Class Application
 *
 * @package addons\Shop\services
 * @property \addons\Shop\services\OrderSyncService $orderSync 订单同步
 */
class Application extends Service
{
    /**
     * @var array
     */
    public $childService = [
            'orderSync' => 'addons\Shop\services\OrderSyncService',            
    ];
}