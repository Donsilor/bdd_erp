<?php

namespace addons\Purchase\services;

use common\components\Service;

/**
 * Class Application
 *
 * @package addons\Purchase\services

 */
class Application extends Service
{
    /**
     * @property addons\Purchase\services\OrderService $order 采购订单
     * @var array
     */
    public $childService = [
            /*********采购单相关*********/
            'order' => 'addons\Purchase\services\OrderService',           
    ];
}