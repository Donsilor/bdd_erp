<?php

namespace addons\Purchase\services;

use common\components\Service;

/**
 * Class Application
 * 
 * @package addons\Purchase\services
 * @property \addons\Purchase\services\PurchaseService $purchase 采购订单
 * @var array
 */
class Application extends Service
{

    public $childService = [
            /*********采购单相关*********/
            'purchase' => 'addons\Purchase\services\PurchaseService',           
    ];
}